<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Tela de verificação de e-mail (não bloqueante: a conta continua usável).
     */
    public function notice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('success', 'Seu e-mail já está confirmado.');
        }

        return view('auth.verify-email');
    }

    /**
     * Confirma o e-mail a partir do link assinado enviado por e-mail.
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('success', 'Seu e-mail já está confirmado.');
        }

        $request->user()->markEmailAsVerified();

        return redirect()->route('dashboard')
            ->with('success', 'E-mail confirmado com sucesso. Obrigado!');
    }

    /**
     * Reenvia o e-mail de verificação.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('success', 'Seu e-mail já está confirmado.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Um novo e-mail de confirmação foi enviado para '.$request->user()->email.'.');
    }
}
