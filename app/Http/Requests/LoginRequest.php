<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rate limiting (5 tentativas por e-mail/IP por minuto).
     */
    protected function failedValidation(Validator $validator)
    {
        $key = Str::lower($this->input('email')).'|'.$this->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => ['Muitas tentativas. Tente novamente em 1 minuto.'],
            ]);
        }

        RateLimiter::hit($key);

        parent::failedValidation($validator);
    }

    /**
     * Limpa tentativas após login bem-sucedido.
     */
    public static function clearAttempts(?string $email, ?string $ip): void
    {
        if ($email && $ip) {
            RateLimiter::clear(Str::lower($email).'|'.$ip);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ];
    }
}
