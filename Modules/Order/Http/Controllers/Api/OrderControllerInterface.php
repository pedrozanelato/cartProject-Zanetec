<?php

namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Order\Http\Requests\StoreOrderRequest;

interface OrderControllerInterface
{
    public function order(StoreOrderRequest $request): JsonResponse;
}
