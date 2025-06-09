<?php

namespace Modules\Order\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property float $totalValue
 * @property string $paymentMethod
 * @property int $times
 * @property Collection $items
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'totalValue' => $this->total_value,
            'paymentMethod' => $this->payment_method,
            'times' => $this->times,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
