<?php

namespace App\Http\Controllers\Messages;

use App\Builder\ReturnApi;
use App\Http\Controllers\Controller;
use App\Http\Requests\Messages\CreateMessageRequest;
use App\Http\Requests\Messages\SendMessageRequest;
use App\Models\Message;
use App\Models\UserHasMessage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{
    public function create(CreateMessageRequest $request)
    {
        return returnApi::Success('Mensagem criada com sucesso.', Message::create($request->validated()), 200);
    }

    // public static function send(SendMessageRequest $request)
    // {
    //     $users = $request['users'];
    //     dd($request);
    //     $message = self::create($request);

    //     foreach ($users as $user) {
    //         UserHasMessage::create([
    //             'user_id' => $user,
    //             'message_id' => $message['data']->id
    //         ]);
    //     }
    //     return ReturnApi::Success('Mensagem enviada com sucesso!', $message['data']);
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
                'user_id' => $user,
                'message_id' => $id,
            ])->first();
            if (!$userHasMessage) return ReturnApi::Error('Usuário não possui a mensagem informada', null, null, 404);
            if ($userHasMessage->readed) return ReturnApi::Error('Mensagem já lida pelo usuário', null, null, 400);

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

            $filters = [
                'user_id' => $user,
                'readed' => false
            ];

            // Paginação
            $per_page = $request->query('per_page', 10);
            $pagination_fields = ['current_page', 'to', 'from', 'per_page', 'total'];

            $messages = UserHasMessage::select(
                'user_has_message.readed',
                'messages.id',
                'messages.title',
                'messages.content',
                'messages.created_at'
            )
                ->where($filters)
                ->join('messages', 'messages.id', '=', 'user_has_message.message_id')
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
