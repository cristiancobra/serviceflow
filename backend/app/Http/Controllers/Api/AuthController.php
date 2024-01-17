<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // A autenticação foi bem-sucedida
            $user = Auth::user();
            $token = $user->createToken('access_token')->plainTextToken;
            // $token = auth()->user()->createToken('teste')->plainTextToken;

            return response()->json([
                'message' => 'Login bem-sucedido',
                'access_token' => $token,
                'user' => $user,
            ]);
        }

        return response()->json([
            'message' => 'Credenciais inválidas',
            // 'message' => $teste,
        ], 401); // 401 indica "Não autorizado"
    }

    public function logout(Request $request)
    {
        {
            $user = $request->user();
            
            // Revoga todos os tokens de acesso do usuário
            $user->tokens->each(function ($token) {
                $token->delete();
            });
        
            // Desloga o usuário
            Auth::guard('web')->logout();
        
            return ['message' => 'Logout bem-sucedido'];
        }
    }
}
