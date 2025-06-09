<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Order\Enums\PaymentTypeEnum;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paymentMethod' => ['required', Rule::in(array_column(PaymentTypeEnum::cases(), 'value'))],
            'times' => 'nullable|integer|min:1',
            'paymentData' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'paymentMethod.required' => 'O método de pagamento é obrigatório.',
            'paymentMethod.in' => 'O método de pagamento informado é inválido.',
            'times.integer' => 'O campo de parcelas deve ser um número inteiro.',
            'times.min' => 'O número mínimo de parcelas deve ser 1.',
            'paymentData.array' => 'Os dados de pagamento devem ser um array.',
        ];
    }

    public function getTipoPagamentoEnum(): PaymentTypeEnum
    {
        return PaymentTypeEnum::fromString($this->input('paymentMethod'));
    }
}
