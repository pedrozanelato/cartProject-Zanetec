<?php

namespace Modules\Cart\Services;

use App\Models\DefaultReturnType;
use Modules\Cart\DTO\CartItemDTO;

interface CartItemServiceInterface
{
    public function create(CartItemDTO $cartItemDTO): DefaultReturnType;

    public function update(CartItemDTO $cartItemDTO): DefaultReturnType;

    public function delete(int $id): DefaultReturnType;
}
