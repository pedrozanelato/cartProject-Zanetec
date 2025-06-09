<?php

namespace App\DTO;

abstract class FillableDTO extends RequestDTO
{
    abstract public function getFillable(): array;
}
