<?php

namespace Modules\Cart\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Cart\DTO\CartItemDTO;

use Modules\Cart\Http\Requests\StoreCartItemRequest;
use Modules\Cart\Http\Requests\UpdateCartItemRequest;

use Modules\Cart\Services\CartItemServiceInterface;

class CartItemController extends Controller implements CartItemControllerInterface
{

    public function __construct(
        private readonly CartItemServiceInterface $cartItemService
    )
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartItemRequest $request): JsonResponse
    {
        return $this->cartItemService->create(CartItemDTO::fromRequest($request))->toJsonResponse();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartItemRequest $request): JsonResponse
    {
        return $this->cartItemService->update(CartItemDTO::fromRequest($request))->toJsonResponse();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->cartItemService->delete($id)->toJsonResponse();
    }
}
