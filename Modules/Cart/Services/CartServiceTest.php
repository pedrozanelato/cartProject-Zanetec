<?php

namespace Modules\Cart\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Cart\Services\CartService;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Product\Entities\Product;
use App\Models\DefaultReturnType;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CartService();

        Product::factory()->create(['unit_price' => 50, 'stock' => 10]);
    }

    public function test_init_creates_and_returns_token_json()
    {
        $response = $this->service->init();

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);

        $data = $response->getData();
        $this->assertTrue(
            property_exists($data, 'cart_token'),
            'Resposta JSON com cart_token'
        );

        $this->assertNotNull($data->cart_token, 'cart_token não pode ser nulo');
    }

    public function test_list_returns_cart_with_items_and_total()
    {
        $cart = Cart::factory()->create(['session_token' => 'my-token']);
        $product = Product::first();
        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $dto = new \App\DTO\FilterDTO(cartToken: 'my-token');
        $result = $this->service->list($dto);

        $this->assertInstanceOf(DefaultReturnType::class, $result);
        $data = $result->data->resolve();
        $this->assertEquals($cart->id, $data['id']);
        $this->assertCount(1, $data['items']);
        $this->assertEquals('100.00', $data['totalValue']);
    }

    public function test_total_items_sums_quantities()
    {
        $cart = Cart::factory()->create(['session_token' => 'tok2']);
        $product = Product::first();
        CartItem::factory()->count(3)->create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $dto = new \App\DTO\FilterDTO(cartToken: 'tok2');
        $result = $this->service->totalItems($dto);

        $this->assertEquals(3, $result->data['count']);
    }

    public function test_get_cart_total_with_various_payment_methods()
    {
        $cart = Cart::factory()->create(['session_token' => 'tok3']);
        $product = Product::first();
        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $totalDefault = $this->service->getCartTotal($cart);
        $this->assertEquals('100.00', $totalDefault);

        $totalPix = $this->service->getCartTotal($cart, \Modules\Order\Enums\PaymentTypeEnum::PIX->value);
        $this->assertEquals('90.00', $totalPix);

        $totalC1 = $this->service->getCartTotal($cart, \Modules\Order\Enums\PaymentTypeEnum::CREDITO_1X->value);
        $this->assertEquals('90.00', $totalC1);

        $totalParc = $this->service->getCartTotal($cart, \Modules\Order\Enums\PaymentTypeEnum::CREDITO_PARCELADO->value, 3);
        $this->assertEquals('103.03', $totalParc);
    }
}
