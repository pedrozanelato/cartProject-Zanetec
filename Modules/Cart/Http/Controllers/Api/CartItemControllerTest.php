<?php

namespace Modules\Cart\Http\Controllers\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Product\Entities\Product;
use Illuminate\Http\Response;

class CartItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Product::factory()->create();
        Cart::factory()->create(['session_token' => 'tokCI']);
    }

    public function test_update_item_success()
    {
        $cartId = Cart::where('session_token', 'tokCI')->value('id');
        $item = CartItem::factory()->create([
            'cart_id'    => $cartId,
            'product_id' => Product::first()->id,
            'quantity'   => 1,
        ]);

        $response = $this->putJson(route('api.carts-items.update', $item->id), ['quantity' => 5]);

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('data.quantity', 5);
    }

    public function test_destroy_item_success()
    {
        $cartId = Cart::where('session_token', 'tokCI')->value('id');
        $item = CartItem::factory()->create([
            'cart_id'    => $cartId,
            'product_id' => Product::first()->id,
        ]);

        $response = $this->deleteJson(route('api.carts-items.destroy', $item->id));

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson(['message' => 'Item removido com sucesso.']);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
