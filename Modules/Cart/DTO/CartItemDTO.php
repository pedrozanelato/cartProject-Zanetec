<?php

namespace Modules\Cart\DTO;

use App\DTO\FillableDTO;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int|null $id
 * @property int|null $cartId
 * @property int|null $productId
 * @property int|null $quantity
 */
class CartItemDTO extends FillableDTO
{
    public function __construct(
        public string|null $cartToken,
        public int|null    $productId,
        public int|null    $quantity,
        public int|null    $id = null,
        public int|null    $cartId = null
    ) {
    }

    public function getFillable(): array
    {
        return [
            'cart_id' => $this->cartId,
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ];
    }

    /**
     * Create a DTO from a request.
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            cartToken: $request->cookie('cart_token') ?? null,
            productId: $request->productId,
            quantity: $request->quantity,
            id: $request->id ?? null
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }
}
