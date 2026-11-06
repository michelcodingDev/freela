<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Autentica o usuário.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $throttleKey = 'login|'.mb_strtolower((string) $credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Muitas tentativas de login. Tente novamente em '.$seconds.' segundos.',
                    'retry_after' => $seconds,
                ], 429);
            }

            return back()
                ->withErrors(['email' => 'Muitas tentativas de login. Tente novamente em '.$seconds.' segundos.'])
                ->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'email' => 'As credenciais informadas não correspondem aos nossos registros.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Bem-vindo de volta, '.auth()->user()->name.'!');
    }

    /**
     * Exibe o formulário de registro.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Registra um novo usuário.
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'type' => $data['type'],
                'status' => 'ativo',
            ]);

            // Atribui role baseado no tipo
            $role = $data['type'] === 'freelancer' ? 'freelancer' : 'cliente';
            $user->assignRole($role);

            // Cria perfil do usuário
            UserProfile::create([
                'user_id' => $user->id,
                'phone' => $data['phone'] ?? null,
                'city' => $data['city'] ?? null,
                'professional_title' => $data['professional_title'] ?? null,
                'bio' => $data['bio'] ?? null,
                'pix_key' => $data['pix_key'] ?? null,
                'rating_average' => 0.00,
                'total_reviews' => 0,
                'total_earned' => 0.00,
                'projects_completed' => 0,
            ]);

            return $user;
        });

        Auth::login($user);

        $user->sendEmailVerificationNotification();

        return redirect()->route('dashboard')
            ->with('success', 'Cadastro realizado com sucesso! Bem-vindo ao FreelaJob. Enviamos um e-mail de confirmação para você.');
    }

    /**
     * Desloga o usuário.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Você saiu com sucesso.');
    }
}
