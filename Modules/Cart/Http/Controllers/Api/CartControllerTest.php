<?php

namespace Modules\Cart\Http\Controllers\Api;

use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Product\Entities\Product;
use Illuminate\Http\Response;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Product::factory()->create();
    }

    public function test_init_endpoint_returns_token()
    {
        $response = $this->getJson(route('api.carts.init'));

        $response->assertStatus(Response::HTTP_OK)
                ->assertJsonStructure(['cart_token'])
                ->assertJsonCount(1);
    }

    public function test_list_returns_error_if_no_cart()
    {
        $response = $this->postJson(route('api.carts.list'));
        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE)
                 ->assertJson(['message' => 'Não foi possível recuperar o carrinho.']);
    }
}