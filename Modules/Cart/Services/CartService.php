<?php

namespace Modules\Cart\Services;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Models\DefaultReturnType;
use App\DTO\FilterDTO;
use Modules\Cart\Entities\Cart;
use Modules\Cart\Transformers\CartResource;
use Modules\Order\Enums\PaymentTypeEnum;

class CartService implements CartServiceInterface
{

    public function list(FilterDTO $cartDto): DefaultReturnType
    {
        $cart = Cart::query()
            ->with('items.cart')
            ->where("session_token", $cartDto->cartToken)
            ->orderBy('created_at', 'desc')
            ->first();
            
        exception(
            condition: empty($cart),
            message: 'Não foi possível recuperar o carrinho.',
            code: Response::HTTP_NOT_ACCEPTABLE
        );
        
        $totalValue = $this->getCartTotal($cart);
        $cart->totalValue = $totalValue;
        
        $defaultReturn = DefaultReturnType::create();
        return $defaultReturn->setData(new CartResource($cart));
    }

    public function init(): JsonResponse
    {
        $token = session('cart_token') 
            ?? request()->cookie('cart_token') 
            ?? tap(Str::uuid()->toString(), fn($t) => session(['cart_token' => $t]));

        $cart = Cart::firstOrCreate(
            ['session_token' => $token],
            ['expires_at' => now()->addHours(12)]
        );

        if ($cart->expires_at && now()->greaterThan($cart->expires_at)) {
            $cart->expires_at = now()->addHours(12);
            $cart->save();
        }
        return response()->json([
            "cart_token" => $cart->session_token
        ])->cookie('cart_token', $cart->session_token, 60 * 12);
    }
    
    /**
     * @throws ExceptionWithData
     */
    public function getCartId(?string $cartToken = null): int
    {
        $cartId = Cart::where('session_token', $cartToken)->value('id');

        exception(
            condition: empty($cartId),
            message: "Não foi possível encontrar o carrinho.",
            code: Response::HTTP_NOT_ACCEPTABLE
        );

        return $cartId;
    }
    
    public function totalItems(FilterDTO $cartDto): DefaultReturnType
    {
        $cart = Cart::query()
            ->with('items.cart')
            ->where("session_token", $cartDto->cartToken)
            ->orderBy('created_at', 'desc')
            ->first();

        exception(
            condition: empty($cart),
            message: 'Não foi possível recuperar o carrinho.',
            code: Response::HTTP_NOT_ACCEPTABLE
        );
        
        $totalItems = $cart?->items->sum('quantity') ?? 0;

        $defaultReturn = DefaultReturnType::create();
        return $defaultReturn->setData(["count" => $totalItems]);
    }
    
    /**
     * @throws ExceptionWithData
     */
    public function getCartTotal(Cart $cart, ?string $methodType = null, ?int $times = 1): string
    {
        $total = '0.00';
        
        foreach ($cart->items as $item) {
            exception(
                condition: !$item->product,
                message: "Não foi possível encontrar o produto do carrinho.",
                code: Response::HTTP_NOT_ACCEPTABLE
            );

            $unitPrice = (string) $item->product->unit_price;
            $quantity = (string) $item->quantity;

            $lineTotal = bcmul($unitPrice, $quantity, 2);
            $total = bcadd($total, $lineTotal, 2);
        }

        switch ($methodType){
            case PaymentTypeEnum::PIX->value:
                // Desconto de 10%
                $discount = bcmul($total, '0.10', 2);
                $total = bcsub($total, $discount, 2);
                break;
                
            case PaymentTypeEnum::CREDITO_1X->value:
                // Desconto de 10%
                $discount = bcmul($total, '0.10', 2);
                $total = bcsub($total, $discount, 2);
                break;

            case PaymentTypeEnum::CREDITO_PARCELADO->value:
                // Juros compostos de 1% ao mês
                $i = '0.01';
                $factor = bcpow(bcadd('1', $i, 10), (string) $times, 10);
                $total = bcmul($total, $factor, 2);
                
                break;
        }

        return $total;
    }
}
