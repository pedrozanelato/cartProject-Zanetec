<?php

namespace Modules\Cart\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Cart\Http\Requests\StoreCartItemRequest;
use Modules\Cart\Http\Requests\UpdateCartItemRequest;

interface CartItemControllerInterface
{
    public function store(StoreCartItemRequest $request): JsonResponse;

    public function update(UpdateCartItemRequest $request): JsonResponse;

    public function destroy(int $id): JsonResponse;
}