<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Registra um novo usuário via API.
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => $data['type'],
            'status' => 'ativo',
            'api_token' => Str::random(80),
        ]);

        // Atribuir role
        $role = $data['type'] === 'freelancer' ? 'freelancer' : 'cliente';
        $user->assignRole($role);

        // Criar perfil
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

        return response()->json([
            'success' => true,
            'user' => new UserResource($user->load('profile')),
            'token' => $user->api_token,
        ], 201);
    }

    /**
     * Login via API.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        $user = Auth::user();
        $user->update(['api_token' => Str::random(80)]);

        return response()->json([
            'success' => true,
            'user' => new UserResource($user->load('profile')),
            'token' => $user->api_token,
        ]);
    }

    /**
     * Logout via API.
     */
    public function logout(Request $request)
    {
        $request->user()->update(['api_token' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Desconectado com sucesso',
        ]);
    }

    /**
     * Retorna o usuário autenticado.
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => new UserResource($request->user()->load('profile')),
        ]);
    }
}
