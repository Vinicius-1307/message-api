<?php

namespace App\Http\Controllers\User;

use App\Builder\ReturnApi;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    // public function update(Request $request)
    // {
    //     $data = $request->all();

    //     $loggedUser = Auth::user();

    //     if (!isset($loggedUser)) return response()->json(['Message' => 'Você não está logado.'], 401);
    //     if (!$loggedUser->is_admin) return response()->json(['Message' => 'Você não tem permissão para criar um usuário.'], 401);
    // }
}
