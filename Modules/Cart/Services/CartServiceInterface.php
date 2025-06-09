<?php

namespace Modules\Cart\Services;

use App\Models\DefaultReturnType;
use App\DTO\FilterDTO;
use Illuminate\Http\JsonResponse;

interface CartServiceInterface
{
    public function list(FilterDTO $cartDto): DefaultReturnType;
    public function init(): JsonResponse;
    public function totalItems(FilterDTO $cartDto): DefaultReturnType;
    public function getCartId(string $cartToken): int;
}
