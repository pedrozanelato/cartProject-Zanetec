<?php

namespace Modules\Cart\Entities;

use App\Traits\CamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Product\Entities\Product;
use Modules\Cart\Database\Factories\CartItemFactory;

/**
 * @CartItem
 * @property int $id
 * @property int $cartId
 * @property int $productId
 * @property int $quantity
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 * @property Carbon $deletedAt
 */
class CartItem extends Model
{
    use HasFactory, CamelCasing;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function newFactory(): CartItemFactory
    {
        return CartItemFactory::new();
    }
}
