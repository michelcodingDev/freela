<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isFreelancer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'offer' => ['required', 'numeric', 'min:1'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:365'],
            'details' => ['required', 'string', 'min:50'],
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
            'offer.required' => 'O valor da proposta é obrigatório.',
            'offer.numeric' => 'Informe um valor válido.',
            'offer.min' => 'O valor mínimo é R$ 1,00.',
            'estimated_days.required' => 'O prazo estimado é obrigatório.',
            'estimated_days.integer' => 'Informe um número válido de dias.',
            'estimated_days.min' => 'O prazo mínimo é 1 dia.',
            'estimated_days.max' => 'O prazo máximo é 365 dias.',
            'details.required' => 'Os detalhes da proposta são obrigatórios.',
            'details.min' => 'Os detalhes devem ter pelo menos 50 caracteres.',
        ];
    }
}
