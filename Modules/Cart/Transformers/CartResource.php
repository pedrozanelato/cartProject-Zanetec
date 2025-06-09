<?php

namespace Modules\Cart\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property Collection $items
 * @property float $totalValue
 */
class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->items),
            'totalValue' => $this->total_value ?? "0.00",
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
