<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Retorna o perfil do usuário autenticado.
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('profile');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
                'status' => $user->status,
                'profile' => $user->profile,
            ],
        ]);
    }

    /**
     * Atualiza o perfil do usuário via API.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'professional_title' => ['nullable', 'string', 'max:255'],
            'pix_key' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            array_filter([
                'phone' => $validated['phone'] ?? null,
                'city' => $validated['city'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'professional_title' => $validated['professional_title'] ?? null,
                'pix_key' => $validated['pix_key'] ?? null,
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso',
            'user' => $user->load('profile'),
        ]);
    }
}
