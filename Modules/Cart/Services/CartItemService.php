<?php

namespace Modules\Cart\Services;

use App\Exceptions\ExceptionWithData;
use App\Models\DefaultReturnType;
use Illuminate\Http\Response;
use Modules\Cart\DTO\CartItemDTO;
use Modules\Cart\Entities\CartItem;
use Modules\Cart\Transformers\CartItemResource;
use Modules\Cart\Services\CartService;

class CartItemService implements CartItemServiceInterface
{

    public function __construct(
        private CartService $cartService
    )
    {
    }

    /**
     * @throws ExceptionWithData
     */
    public function create(CartItemDTO $cartItemDto): DefaultReturnType
    {
        $cartId = $this->cartService->getCartId($cartItemDto->cartToken);
        $cartItemDto->cartId = $cartId;

        $itemExist = CartItem::where('cart_id', $cartId)
            ->where('product_id', $cartItemDto->productId)
            ->first();

        if ($itemExist) {
            $itemExist->quantity += $cartItemDto->quantity;

            exception(
                condition: !$itemExist->save(),
                message: 'Não foi possível atualizar a quantidade do item.',
                code: Response::HTTP_NOT_ACCEPTABLE
            );

            $cartItem = $itemExist;
        } else {
            $cartItem = new CartItem($cartItemDto->getFillable());

            exception(
                condition: !$cartItem->save(),
                message: 'Não foi possível adicionar o item ao carrinho.',
                code: Response::HTTP_NOT_ACCEPTABLE
            );
        }
        
        return DefaultReturnType::create()
            ->setCode(Response::HTTP_CREATED)
            ->setData(CartItemResource::make($cartItem))
            ->setMessage('Item adicionado com sucesso!');
    }

    /**
     * @throws ExceptionWithData
     */
    public function update(CartItemDTO $cartItemDto): DefaultReturnType
    {
        $cartItem = $this->getById(id: $cartItemDto->id());

        $item = $cartItemDto->getFillable();
        unset($item['cart_id']);
        unset($item['product_id']);
        
        exception(
            condition: !$cartItem->update($item),
            message: 'Não foi possível atualizar o carrinho.',
            code: Response::HTTP_NOT_ACCEPTABLE
        );

        return DefaultReturnType::create()
            ->setMessage('Carrinho atualizado com sucesso.')
            ->setData(CartItemResource::make($cartItem->refresh()));
    }

    /**
     * @throws ExceptionWithData
     */
    public function delete(int $id): DefaultReturnType
    {
        $cartItem = $this->getById(id: $id);

        exception(
            condition: !$cartItem->delete(),
            message: 'Não foi possível remover o item do carrinho.',
            code: Response::HTTP_NOT_ACCEPTABLE
        );

        return DefaultReturnType::create()
            ->setMessage('Item removido com sucesso.');
    }

    /**
     * @throws ExceptionWithData
     */
    private function getById(int $id): CartItem
    {
        /**
         * @var Cart $cartItem
         */
        $cartItem = CartItem::query()->find($id);

        exception(
            condition: empty($cartItem),
            message: 'Item não encontrado.',
            code: Response::HTTP_NOT_ACCEPTABLE
        );

        return $cartItem;
    }
}
