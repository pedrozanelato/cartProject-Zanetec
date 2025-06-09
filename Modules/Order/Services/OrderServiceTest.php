<?php

namespace Modules\Order\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Order\Services\OrderService;
use Modules\Order\DTO\OrderDTO;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Product\Entities\Product;
use Illuminate\Http\Response;
use App\Models\DefaultReturnType;
use App\Exceptions\ExceptionWithData;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Product::factory()->create(['unit_price' => 20, 'stock' => 100]);

        $cart = Cart::factory()->create(['session_token' => 'token-order']);
        CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => Product::first()->id,
            'quantity'   => 3, 
        ]);

        $this->service = new OrderService(app(\Modules\Cart\Services\CartServiceInterface::class));
    }

    public function test_order_success_creates_order_and_items()
    {
        $dto = new OrderDTO(
            cartToken: 'token-order',
            paymentMethod: \Modules\Order\Enums\PaymentTypeEnum::PIX->value,
            times: 1,
            paymentData: ['teste' => 'teste']
        );

        $result = $this->service->order($dto);

        $this->assertEquals(Response::HTTP_CREATED, $result->code);
        $this->assertEquals('Pedido criado com sucesso!', $result->message);

        $orderArray = $result->data->resolve();
        $this->assertArrayHasKey('id', $orderArray);

        $this->assertDatabaseHas('orders', [
            'total_value'    => '54.00',
            'payment_method' => \Modules\Order\Enums\PaymentTypeEnum::PIX->value,
            'times'          => 1,
        ]);
        $this->assertDatabaseHas('order_items', [
            'quantity'   => 3,
            'unit_price' => '20.00',
        ]);
    }

    public function test_order_without_cart_throws_exception()
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $dto = new OrderDTO(
            cartToken: 'nonexistent-token',
            paymentMethod: \Modules\Order\Enums\PaymentTypeEnum::PIX->value
        );

        $this->service->order($dto);
    }
}
