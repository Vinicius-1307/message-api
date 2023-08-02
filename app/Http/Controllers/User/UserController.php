<?php

namespace App\Http\Controllers;

use App\Builder\ReturnApi;
use App\Models\User;
use App\Services\Auth\LoginService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $loggedUser = Auth::user();

        if (!isset($loggedUser)) return response()->json(['Message' => 'Você não está logado.'], 401);

        if (!$loggedUser->is_admin) return response()->json(['Message' => 'Você não tem permissão para criar um usuário.'], 401);

        $verifyEmailAlreadyExist = User::where('email', $data['email'])->first();

        if ($verifyEmailAlreadyExist) return response()->json(['Message' => 'Esse email já existe.'], 400);
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password'])
            ]);
            return response()->json(['Message' => 'Usuário criado com sucesso.'], 201);
        } catch (\Throwable $th) {
            return response()->json(['Message' => 'Erro ao criar usuário.', 'DevMessage' => $th->getMessage()], 400);
        }
    }

    public function login(Request $request)
    {
        $data = $request->all();

        $verifyEmailAlreadyExist = User::where('email', $data['email'])->first();

        if (!$verifyEmailAlreadyExist) return response()->json(['Message' => 'Usuário não encontrado.'], 400);

        if (!password_verify($data['password'], $verifyEmailAlreadyExist->password)) {
            return response()->json(['Message' => 'Credenciais incorretas.'], 400);
        }

        return response()->json(['Message' => 'Usuário logado com sucesso', 'data' => $verifyEmailAlreadyExist], 200);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $loggedUser = Auth::user();

        if (!isset($loggedUser)) return response()->json(['Message' => 'Você não está logado.'], 401);
        if (!$loggedUser->is_admin) return response()->json(['Message' => 'Você não tem permissão para criar um usuário.'], 401);
    }
}
