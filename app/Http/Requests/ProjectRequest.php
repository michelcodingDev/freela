<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isCliente();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'experience_level' => ['required', 'in:iniciante,intermediario,especialista'],
            'visibility' => ['required', 'in:publico,privado'],
            'proposal_deadline' => ['required', 'date', 'after:today'],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'], // 10MB
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
            'category_id.required' => 'Selecione uma categoria.',
            'title.required' => 'O título é obrigatório.',
            'description.required' => 'A descrição é obrigatória.',
            'description.min' => 'A descrição deve ter pelo menos 50 caracteres.',
            'experience_level.required' => 'Selecione o nível de experiência.',
            'proposal_deadline.required' => 'Informe o prazo para propostas.',
            'proposal_deadline.after' => 'O prazo deve ser uma data futura.',
            'budget_max.gte' => 'O orçamento máximo deve ser maior ou igual ao mínimo.',
        ];
    }
}
