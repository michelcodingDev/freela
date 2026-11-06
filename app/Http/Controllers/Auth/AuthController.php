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

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'As credenciais informadas não correspondem aos nossos registros.',
            ])->onlyInput('email');
        }

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

    /**
     * Registra um novo usuário.
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
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
        });

        Auth::login(User::where('email', $data['email'])->first());

        return redirect()->route('dashboard')
            ->with('success', 'Cadastro realizado com sucesso! Bem-vindo ao FreelaJob.');
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
