<?php

namespace App\Http\Controllers;

use App\Builder\ReturnApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        $validators = [
            'email' => 'required|email',
            'password' => 'required|min:4',
        ];

        $messages = [
            'email.required' => 'Email não inserido',
            'password.required' => 'Senha não inserida',
            'password.min' => 'Insira uma senha de no mínimo 6 caracteres'
        ];

        $isValidated = \Illuminate\Support\Facades\Validator::make($credentials, $validators, $messages);

        if ($isValidated->fails()) {
            return ReturnApi::messageReturn(true, $isValidated->errors()->first(), null, null, null, 400);
        }

        if (!$token = Auth::attempt($credentials)) {
            return ReturnApi::messageReturn(true, "Email ou senha incorretos", 'wrong credentials', null, null, 401);
        }
    }

    protected function respondWithToken($token)
    {
        $content = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 1440
        ];

        return ReturnApi::messageReturn(false, null, null, null, $content, 200);
    }

    public function logout()
    {
        Auth::logout();

        return ReturnApi::messageReturn(false, "Logoff realizado com sucesso", null, null, null, 200);
    }
}
