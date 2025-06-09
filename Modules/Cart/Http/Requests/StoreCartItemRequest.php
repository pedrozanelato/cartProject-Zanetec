<?php

namespace Modules\Cart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $productId
 * @property int $quantity
 */

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'productId' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'productId.required' => 'O ID do produto é obrigatório.',
            'productId.integer' => 'O ID do produto deve ser um número inteiro.',
            'productId.exists' => 'O ID do produto informado não é válido.',
            
            'quantity.required' => 'A quantidade do produto é obrigatório.',
            'quantity.integer' => 'A quantidade do produto deve ser um número inteiro.',
            'quantity.min' => 'A quantidade do produto deve ser maior que 0.',
        ];
    }
}
