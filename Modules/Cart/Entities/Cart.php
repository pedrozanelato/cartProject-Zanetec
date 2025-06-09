<?php

namespace Modules\Cart\Entities;

use App\Traits\CamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Cart\Database\Factories\CartFactory;

/**
 * @Cart
 * @property int $id
 * @property string $session_token
 * @property Carbon $expiresAt
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 * @property Carbon $deletedAt
 */
class Cart extends Model
{
    use HasFactory, CamelCasing, SoftDeletes;

    protected $fillable = [
        'session_token',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    
    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
    }
}
