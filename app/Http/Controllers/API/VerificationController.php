<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Reenvia o e-mail de confirmação para o usuário autenticado via API.
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Seu e-mail já está confirmado.',
                'email_verified' => true,
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Um novo e-mail de confirmação foi enviado para '.$user->email.'.',
            'email_verified' => false,
        ]);
    }

    /**
     * Retorna o status atual de verificação de e-mail.
     */
    public function status(Request $request)
    {
        return response()->json([
            'success' => true,
            'email' => $request->user()->email,
            'email_verified' => $request->user()->hasVerifiedEmail(),
        ]);
    }
}
