<?php

namespace Modules\Order\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Models\DefaultReturnType;
use Modules\Order\DTO\OrderDTO;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Entities\CartItem;
use Modules\Order\Entities\Order;
use Modules\Order\Transformers\OrderResource;
use Modules\Cart\Services\CartServiceInterface;

class OrderService implements OrderServiceInterface
{
    public function __construct(
        private CartServiceInterface $cartService
    )
    {
    }

    public function order(OrderDTO $orderDto): DefaultReturnType
    {
        $cart = Cart::query()
            ->with('items.product')
            ->where("session_token", $orderDto->cartToken)
            ->first();
        
        exception(
            condition: empty($cart),
            message: 'Não foi possível recuperar o carrinho.',
            code: Response::HTTP_NOT_ACCEPTABLE
        );

        $totalValue = $this->cartService->getCartTotal($cart, $orderDto->paymentMethod, $orderDto->times);

        $orderDto->totalValue = $totalValue;
        $order = new Order($orderDto->getFillable());

        $itemsArray = $cart->items->map(fn(CartItem $i) => [
            'product_id' => $i->product_id,
            'quantity' => $i->quantity,
            'unit_price' => $i->product->unit_price,
        ])->toArray();

        try {
            DB::transaction(function () use ($order, $itemsArray) {
                if (!$order->save()) {
                    throw new Exception("Não foi possível criar o pedido", Response::HTTP_NOT_ACCEPTABLE);
                }

                foreach ($itemsArray as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
            });

            return DefaultReturnType::create()
                ->setCode(Response::HTTP_CREATED)
                ->setData(OrderResource::make($order))
                ->setMessage('Pedido criado com sucesso!');
        } catch (\Throwable $e) {

            Log::error("OrderService", [
                'session_token' => $orderDto->cartToken ?? null,
                'cart_id' => $cart->id ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            exception(
                condition: true,
                message: 'Não foi possível criar o pedido.',
                code: Response::HTTP_NOT_ACCEPTABLE
            );
        }
    }

    public function init(): JsonResponse
    {
        $token = session('order_token') 
            ?? request()->cookie('order_token') 
            ?? tap(Str::uuid()->toString(), fn($t) => session(['order_token' => $t]));

        $order = Order::firstOrCreate(
            ['session_token' => $token],
            ['expires_at' => now()->addHours(12)]
        );

        if ($order->expires_at && now()->greaterThan($order->expires_at)) {
            $order->expires_at = now()->addHours(12);
            $order->save();
            
            return response()->json([
                "cart_token" => $cart->session_token
            ])->cookie('cart_token', $cart->session_token, 60 * 12);
        }else{
            
            return response()->json([
                "cart_token" => $cart->session_token
            ]);
        }
    }
    
    /**
     * @throws ExceptionWithData
     */
    public function getOrderId(string $orderToken): int
    {
        $orderId = Order::where('session_token', $orderToken)->value('id');

        exception(
            condition: empty($orderId),
            message: "Não foi possível encontrar o carrinho.",
            code: Response::HTTP_NOT_ACCEPTABLE
        );

        return $orderId;
    }
}
