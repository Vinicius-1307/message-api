<?php

namespace App\Http\Controllers\Auth;

use App\Builder\ReturnApi;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) return ReturnApi::Error("Usuário ou senha incorretos", 401);

        $user = User::find(auth('api')->user()->id);

        return ReturnApi::Success("Usuário autenticado com sucesso", array('user' => $user, "token" => $token), 200);
    }
}
