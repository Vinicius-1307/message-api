<?php

namespace App\Http\Controllers\Messages;

use App\Builder\ReturnApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\Message;
use App\Models\UserHasMessage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    public function create(MessageRequest $request)
    {
        try {
            $loggedUser = (Auth::user());
            if (!$loggedUser->is_admin) return ReturnApi::Error("Você não tem permissão para criar uma mensagem.", null, null, 401);

            $data = $request->validated();

            //Create message
            $message = Message::create(
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'created_at' => now()
                ]
            );

            return ['error' => false, 'data' => $message];
        } catch (Exception $err) {
            return ['error' => true, 'message' => $err->getMessage()];
        }
    }

    /**
     * Create message and associate with user
     * @param Request $request Payload data
     * @return JsonResponse
     */
    // public static function send(Request $request)
    // {
    //     try {
    //         $data = $request->all();

    //         $validate = Validator();
    //         if ($validate->fails()) return ReturnApi::Error($validate->errors()->first(), null, null, 409);

    //         $users = $data['users'];

    //         $message = self::create($data);
    //         if ($message['error']) return ReturnApi::Error($message['message'], null, null, 409);

    //         foreach ($users as $user) {
    //             UserHasMessage::create([
    //                 'user' => $user,
    //                 'message' => $message['data']->id
    //             ]);
    //         }
    //     } catch (Exception $err) {
    //         return ReturnApi::Error($err->getMessage(), 500);
    //     }
    // }

    /**
     * Mark message as readed
     * @param integer $id Message identifier
     * @return JsonResponse
     */
    public static function read($id)
    {
        try {
            $message = Message::find($id);
            if (!$message) return ReturnApi::Error('Mensagem não encontrada', null, null, 404);

            $user = Auth::id();

            $userHasMessage = UserHasMessage::where([
                'user' => $user,
                'message' => $id,
            ])->first();
            if (!$userHasMessage) return ReturnApi::Error('Usuário não possui a mensagem informada', null, null, 404);
            if ($userHasMessage->readed) return ReturnApi::Error('Mensagem já lida pelo usuário', null, null, 409);

            $userHasMessage->readed = true;
            $userHasMessage->readed_at = date('Y-m-d H:i:s');
            $userHasMessage->save();

            return ReturnApi::Success('Mensagem marcada como lida!', $userHasMessage);
        } catch (Exception $err) {
            return ReturnApi::Error($err->getMessage(), 500);
        }
    }

    /**
     * List all messages from user
     * @param Request $request Payload data
     * @return JsonResponse
     */
    public static function list(Request $request)
    {
        try {
            $user = Auth::id();

            $filters = ['user' => $user];

            $readed = $request->query('readed', null);

            if (isset($readed) && $readed != '' && ($readed == "1" || $readed == "0")) {
                $filters['user_has_message.readed'] = $readed;
            }

            // Paginação
            $per_page = $request->query('per_page', 10);
            $pagination_fields = ['current_page', 'to', 'from', 'per_page', 'total'];

            $messages = UserHasMessage::select(
                'user_has_message.readed',
                'user_has_message.readed_at',
                'messages.id',
                'messages.title',
                'messages.content',
                'messages.created_at'
            )
                ->where($filters)
                ->join('messages', 'messages.id', '=', 'user_has_message.message')
                ->orderByRaw("user_has_message.readed ASC, messages.created_at DESC")
                ->paginate($per_page)->toArray();

            // Simplificando retorno
            $data['messages'] = $messages['data'];
            foreach ($pagination_fields as $f) $data['pagination'][$f] = $messages[$f];

            return ReturnApi::Success('Mensagens listadas com sucesso!', $data);
        } catch (Exception $err) {
            return ReturnApi::Error($err->getMessage(), 500);
        }
    }

    /**
     * Delete message
     * @param integer $id Message identifier
     * @return JsonResponse
     */
    public static function delete($id)
    {
        try {
            $hasPermission = (Auth::id());
            if (!$hasPermission) return ReturnApi::Error('Você não tem permissão para deletar mensagens', null, null, 403);

            $message = Message::find($id);
            if (!$message) return ReturnApi::Error('Mensagem não encontrada', null, null, 404);

            // DANGER
            $message->delete();

            return ReturnApi::Success('Mensagem deletada com sucesso!', $message);
        } catch (Exception $err) {
            return ReturnApi::Error($err->getMessage(), 500);
        }
    }
}