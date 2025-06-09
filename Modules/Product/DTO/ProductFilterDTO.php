<?php

namespace Modules\Product\DTO;

use App\DTO\RequestDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property int|null $id
 * @property string|null $name
 * @property float|null $unitPrice
 * @property UploadedFile|null $file
 * @property int|null $perPage
 * @property int|null $page
 */
class ProductFilterDTO extends RequestDTO
{
    public function __construct(
        public ?UploadedFile $file = null,
        public ?string $filePath = null,
        public ?name $name = null, 
        public ?float $unitPrice = null, 
        public ?int $id = null,
        public ?int $perPage = null,
        public ?int $page = null,
        public ?bool $withPaginate = true
    ) {
    }

    /**
     * Create a DTO from a request.
     */
    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            file: $request->hasFile('file') ? $request->file('file') : null,
            filePath: null,
            name: $request->name,
            unitPrice: $request->unitPrice,
            id: $request->id ?? null,
            perPage: $request->integer('perPage'),
            page: $request->integer('page'),
            withPaginate: $request->boolean('withPaginate', true)
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
