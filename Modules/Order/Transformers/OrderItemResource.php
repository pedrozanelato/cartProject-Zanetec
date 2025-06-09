<?php

namespace Modules\Order\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
// use Modules\Product\Transformers\ProductResource;

/**
 * @property int $id
 * @property string $name
 * @property float $price
 * @property string $file
 */
class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // 'product' => new ProductResource($this->product),
            'quantity' => $this->quantity,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
