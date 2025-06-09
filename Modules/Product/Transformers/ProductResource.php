<?php

namespace Modules\Product\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $name
 * @property float $unit_price
 * @property string $file
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unitPrice' => $this->unit_price,
            'file' => $this->file ? Storage::disk('public')->url($this->file) : null,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
