<?php

namespace App\Http\Controllers\User;

use App\Builder\ReturnApi;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $verifyEmailAlreadyExist = User::where('email', $data['email'])->first();

        if ($verifyEmailAlreadyExist) return ReturnApi::Error('Esse e-mail já existe', 400);
        try {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password'])
            ]);
            return ReturnApi::Success('Usuário criado com sucesso.', 201);
        } catch (\Throwable $th) {
            return ReturnApi::Error('Erro ao criar usuário.', $th->getMessage(), 400);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        try {
            $user = User::find($id);

            if (!isset($user)) return ReturnApi::Error("Usuário não encontrado", null, 404);

            $oldUser = $user;

            $rules = [
                'name' => 'required',
                'email' => 'required',
                'password' => 'required'
            ];

            $messages = [
                'name.required' => 'Nome do banco deve ser informado',
                'email.required' => 'Novo e-mail deve ser informado',
                'password.required' => 'Nova senha deve ser informado'
            ];

            $validate = Validator::make($data, $rules, $messages);

            if ($validate->fails()) return ReturnApi::Error($validate->errors()->first(), $validate->errors()->first(), null, 424);

            foreach ($user->toArray() as $key => $value) $user[$key] = !isset($data[$key]) ? $value : $data[$key];

            $user->update();

            return ReturnApi::Success('Usuário atualizado com sucesso!', $user);
        } catch (\Exception $error) {
            $oldUser->update();
            return ReturnApi::Error('Erro ao atualizar usuário', $error->getMessage(), 500);
        }
    }

    public function list(Request $request)
    {
        $users = User::all();

        if (!isset($users)) {
            return ReturnApi::Error("Não há nenhum usuário cadastrado", null);
        }
        return (['error' => false, 'users' => $users]);
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if (!$user) return ReturnApi::Error("Usuário não encontrado.", null, null, 404);

            $user->delete();

            return ReturnApi::Success("Usuário deletado com sucesso.", $user);
        } catch (Exception $th) {
            return ReturnApi::Error($th->getMessage(), 500);
        }
    }
}
