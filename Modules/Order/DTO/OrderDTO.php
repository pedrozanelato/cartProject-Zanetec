<?php

namespace Modules\Order\DTO;

use App\DTO\FillableDTO;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int|null $id
 * @property string|null $paymentMethod
 * @property int|null $times
 * @property array|null $paymentData
 */
class OrderDTO extends FillableDTO
{
    public function __construct(
        public string|null $cartToken,
        public string|null $paymentMethod,
        public int|null    $times = 1,
        public float|null $totalValue = null, 
        public array|null  $paymentData = [],
        public int|null    $id = null
    ) {
    }

    public function getFillable(): array
    {
        return [
            'total_value' => $this->totalValue,
            'payment_method' => $this->paymentMethod,
            'times' => $this->times,
            'paymentData' => $this->paymentData,
        ];
    }

    /**
     * Create a DTO from a request.
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            cartToken: $request->cookie('cart_token') ?? null,
            paymentMethod: $request->paymentMethod,
            times: $request->times ?? 1,
            paymentData: $request->paymentData ?? [],
            id: $request->id ?? null
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }
}
