<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\Api\ProductControllerInterface;

Route::post('products/list', [ProductControllerInterface::class, 'list'])->name('api.products.list');
Route::resource('products', ProductControllerInterface::class)
    ->only(['show'])
    ->parameter('products', 'id');