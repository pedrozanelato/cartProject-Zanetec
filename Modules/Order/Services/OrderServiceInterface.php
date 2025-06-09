<?php

namespace Modules\Order\Services;

use App\Models\DefaultReturnType;
use Modules\Order\DTO\OrderDTO;

interface OrderServiceInterface
{
    public function order(OrderDTO $orderDto): DefaultReturnType;
}
