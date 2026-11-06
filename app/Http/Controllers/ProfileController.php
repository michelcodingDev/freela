<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Exibe o perfil do usuário logado.
     */
    public function show()
    {
        $user = Auth::user()->load('profile');

        return view('profile.show', compact('user'));
    }

    /**
     * Exibe o formulário de edição do perfil.
     */
    public function edit()
    {
        $user = Auth::user()->load('profile');

        return view('profile.edit', compact('user'));
    }

    /**
     * Atualiza o perfil do usuário.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'professional_title' => ['nullable', 'string', 'max:255'],
            'pix_key' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_type' => ['nullable', 'string', 'max:50'],
            'bank_agency' => ['nullable', 'string', 'max:20'],
            'bank_account' => ['nullable', 'string', 'max:50'],
            'bank_account_digit' => ['nullable', 'string', 'max:10'],
            'cpf_cnpj' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        // Atualiza usuário
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Atualiza ou cria perfil
        $profileData = array_filter([
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'professional_title' => $validated['professional_title'] ?? null,
            'pix_key' => $validated['pix_key'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_type' => $validated['bank_account_type'] ?? null,
            'bank_agency' => $validated['bank_agency'] ?? null,
            'bank_account' => $validated['bank_account'] ?? null,
            'bank_account_digit' => $validated['bank_account_digit'] ?? null,
            'cpf_cnpj' => $validated['cpf_cnpj'] ?? null,
        ]);

        // Upload de foto
        if ($request->hasFile('profile_photo')) {
            if ($user->profile?->profile_photo) {
                Storage::delete('public/'.$user->profile->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $profileData['profile_photo'] = $path;
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('profile.show')
            ->with('success', 'Perfil atualizado com sucesso!');
    }
}
