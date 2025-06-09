<?php

namespace Modules\Product\Entities;

use App\Traits\CamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Product\Database\Factories\ProductFactory;

/**
 * @Product
 * @property int $id
 * @property string $name
 * @property float $unitPrice
 * @property int $stock
 * @property string $file
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 * @property Carbon $deletedAt
 */
class Product extends Model
{
    use HasFactory, CamelCasing;

    protected $fillable = [
        'name',
        'unit_price',
        'stock',
        'file',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
