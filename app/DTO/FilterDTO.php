<?php

namespace App\DTO;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int|null $perPage
 * @property int|null $page
 * @property string|null $cartToken
 */
class FilterDTO extends RequestDTO
{
    public function __construct(
        public ?int $perPage = null,
        public ?int $page = null,
        public ?bool $withPaginate = true,
        public ?string $cartToken = null
    ) {
    }

    /**
     * Create a DTO from a request.
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            perPage: $request->integer('perPage'),
            page: $request->integer('page'),
            withPaginate: $request->boolean('withPaginate', true),
            cartToken: $request->cookie('cart_token') ?? null,
        );
    }
    
    /**
     * @return int|null
     */
    public function id(): ?int
    {
        return $this->id;
    }
}
