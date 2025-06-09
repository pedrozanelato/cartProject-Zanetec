<?php

namespace Modules\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $quantity
 */

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => 'A quantidade do produto é obrigatório.',
            'quantity.integer' => 'A quantidade do produto deve ser um número inteiro.',
            'quantity.min' => 'A quantidade do produto deve ser maior que 0.',
        ];
    }
}
