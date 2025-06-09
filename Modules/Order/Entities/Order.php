<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Order\Database\Factories\OrderFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_value',
        'payment_method',
        'times',
        'payment_data',
    ];

    protected $casts = [
        'total_value'   => 'decimal:2',
        'payment_data'  => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    
    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
