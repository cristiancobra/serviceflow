<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function checkToken(Request $request)
    {
        return response()->json(['valid' => true]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // A autenticação foi bem-sucedida
            // $token = $user->createToken('access_token')->plainTextToken;

            return response()->json([
                'message' => 'Login bem-sucedido',
                // 'access_token' => $token,
                'user' => Auth::user(),
            ]);
        }

        return response()->json([
            'message' => 'Credenciais inválidas',
            // 'message' => $teste,
        ], 401); // 401 indica "Não autorizado"
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout bem-sucedido']);
    }
}
