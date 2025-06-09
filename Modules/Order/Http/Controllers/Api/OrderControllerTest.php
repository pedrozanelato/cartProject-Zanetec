<?php

namespace Modules\Order\Http\Controllers\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Product\Entities\Product;
use Illuminate\Http\Response;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Product::factory()->create(['unit_price' => 50, 'stock' => 20]);
        $cart = Cart::factory()->create(['session_token' => 'feat-token']);
        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => Product::first()->id,
            'quantity'   => 2,
        ]);
    }

    public function test_place_order_without_cart_returns_error()
    {
        $payload = [
            'paymentMethod' => 'pix',
            'times'         => 1
        ];

        $response = $this
            ->postJson(route('api.orders.order'), $payload);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE)
                 ->assertJson(['message' => 'Não foi possível recuperar o carrinho.']);
    }
}
