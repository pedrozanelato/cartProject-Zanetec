<?php

namespace App\DTO;

use Illuminate\Foundation\Http\FormRequest;

abstract class RequestDTO
{
    abstract public static function fromRequest(FormRequest $request): self;
}
