<?php

namespace Modules\Cart\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Controller;
use App\DTO\FilterDTO;
use Modules\Cart\Services\CartServiceInterface;

class CartController extends Controller implements CartControllerInterface
{
    public function __construct(
        private CartServiceInterface $cartService
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function list(FormRequest $request): JsonResponse
    {
        return $this->cartService->list(FilterDTO::fromRequest($request))->toJsonResponse();
    }

    public function init(): JsonResponse
    {
        return $this->cartService
            ->init();
    }

    public function totalItems(FormRequest $request): JsonResponse
    {
        return $this->cartService
            ->totalItems(FilterDTO::fromRequest($request))->toJsonResponse();
    }
}
