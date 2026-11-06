<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'price_monthly' => ['required', 'numeric', 'min:0'],
            'commission_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'min_commission' => ['required', 'numeric', 'min:0'],
            'proposals_per_month' => ['required', 'integer', 'min:1'],
            'max_portfolio_items' => ['required', 'integer', 'min:1'],
            'withdrawal_days' => ['required', 'integer', 'min:1'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
            'features' => ['nullable', 'array'],
            'features.*.name' => ['required', 'string', 'max:255'],
            'features.*.description' => ['required', 'string'],
            'features.*.is_included' => ['boolean'],
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
            'name.required' => 'O nome do plano é obrigatório.',
            'price_monthly.required' => 'O preço mensal é obrigatório.',
            'commission_percentage.required' => 'A porcentagem de comissão é obrigatória.',
            'proposals_per_month.required' => 'O limite de propostas é obrigatório.',
            'max_portfolio_items.required' => 'O limite de portfólio é obrigatório.',
            'withdrawal_days.required' => 'Os dias para saque são obrigatórios.',
        ];
    }
}
