<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Exibe o perfil do usuário logado.
     */
    public function show()
    {
        $user = Auth::user()->load(['profile', 'skills']);

        return view('profile.show', compact('user'));
    }

    /**
     * Exibe o formulário de edição do perfil.
     */
    public function edit()
    {
        $user = Auth::user()->load(['profile', 'skills']);
        $skills = Skill::with('category')->orderBy('name')->get();

        return view('profile.edit', compact('user', 'skills'));
    }

    /**
     * Atualiza o perfil do usuário.
     *
     * Trocar o e-mail invalida a verificação anterior e dispara uma nova
     * confirmação (não bloqueante). Campos enviados vazios são limpos.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:2'],
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
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ]);

        $emailChanged = mb_strtolower($validated['email']) !== mb_strtolower((string) $user->email);

        // Atualiza usuário (email_verified_at não é fillable: usa forceFill)
        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ])->save();

        // Atualiza ou cria perfil (campos vazios limpam o valor)
        $profileData = [
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
        ];

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

        if ($user->isFreelancer()) {
            $user->skills()->sync($validated['skills'] ?? []);
        }

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return redirect()->route('profile.show')
            ->with('success', $emailChanged
                ? 'Perfil atualizado. Enviamos uma nova confirmação de e-mail.'
                : 'Perfil atualizado com sucesso!');
    }

    /**
     * Altera a senha do usuário logado.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Informe sua senha atual.',
            'current_password.current_password' => 'A senha atual está incorreta.',
            'password.required' => 'Informe a nova senha.',
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
        ]);

        $user->forceFill(['password' => Hash::make($validated['password'])]);

        $user->setRememberToken(Str::random(60));
        $user->save();

        Auth::logoutOtherDevices($validated['password']);

        return redirect()->route('profile.edit')
            ->with('success', 'Senha alterada com sucesso.');
    }
}
