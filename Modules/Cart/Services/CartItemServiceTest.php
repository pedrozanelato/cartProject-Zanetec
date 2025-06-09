<?php

namespace Modules\Cart\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Cart\Services\CartItemService;
use Modules\Cart\Services\CartService;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Product\Entities\Product;
use Modules\Cart\DTO\CartItemDTO;
use App\Exceptions\ExceptionWithData;
use Illuminate\Http\Response;

class CartItemServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartItemService $service;
    private CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();

        $product = Product::factory()->create();
        $cart = Cart::factory()->create(['session_token' => 'tokenX']);

        $this->cartService = new CartService();
        $this->service = new CartItemService($this->cartService);
    }

    public function test_create_new_item_success()
    {
        $dto = new CartItemDTO(cartToken: 'tokenX', productId: Product::first()->id, quantity: 3);

        $result = $this->service->create($dto);
        $this->assertEquals(Response::HTTP_CREATED, $result->code);
        $this->assertDatabaseHas('cart_items', [
            'quantity'   => 3,
            'product_id' => $dto->productId,
        ]);
    }

    public function test_create_existing_item_increments_quantity()
    {
        $cartId = Cart::where('session_token', 'tokenX')->value('id');
        CartItem::factory()->create(['cart_id' => $cartId, 'product_id' => Product::first()->id, 'quantity' => 2]);

        $dto = new CartItemDTO(cartToken: 'tokenX', productId: Product::first()->id, quantity: 5);
        $result = $this->service->create($dto);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $dto->productId,
            'quantity'   => 7,
        ]);
    }

    public function test_update_item_success()
    {
        $cartId = Cart::where('session_token', 'tokenX')->value('id');
        $item = CartItem::factory()->create(['cart_id' => $cartId, 'product_id' => Product::first()->id, 'quantity' => 1]);

        $dto = new CartItemDTO(cartToken: 'tokenX', productId: null, quantity: 10, id: $item->id);
        $result = $this->service->update($dto);

        $this->assertEquals('Carrinho atualizado com sucesso.', $result->message);
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 10]);
    }

    public function test_destroy_nonexistent_returns_not_found()
    {
        $response = $this->deleteJson(route('api.carts-items.destroy', 999));

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE)
                ->assertJson([
                    'message' => 'Item não encontrado.'
                ]);
    }


    public function test_delete_item_success()
    {
        $cartId = Cart::where('session_token', 'tokenX')->value('id');
        $item = CartItem::factory()->create(['cart_id' => $cartId, 'product_id' => Product::first()->id]);

        $result = $this->service->delete($item->id);
        $this->assertEquals('Item removido com sucesso.', $result->message);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }
}
