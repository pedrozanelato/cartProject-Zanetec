<?php

namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Order\Http\Requests\StoreOrderRequest;
use App\Http\Controllers\Controller;
use Modules\Order\DTO\OrderDTO;
use Modules\Order\Services\OrderServiceInterface;

class OrderController extends Controller implements OrderControllerInterface
{
    public function __construct(
        private OrderServiceInterface $orderService
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function order(StoreOrderRequest $request): JsonResponse
    {
        return $this->orderService->order(OrderDTO::fromRequest($request))->toJsonResponse();
    }
}
