<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait CamelCasing
{
    public bool $enforceCamelCase = true;

    protected function isGuardableColumn($key): bool
    {
        return parent::isGuardableColumn($this->getSnakeKey($key));
    }

    public function setAttribute($key, $value): mixed
    {
        return parent::setAttribute($this->getSnakeKey($key), $value);
    }

    public function getAttribute($key): mixed
    {
        if (method_exists($this, $key)) {
            return $this->getRelationValue($key);
        }

        return parent::getAttribute($this->getSnakeKey($key));
    }

    public function attributesToArray(): array
    {
        return $this->toCamelCase(parent::attributesToArray());
    }

    public function relationsToArray(): array
    {
        return $this->toCamelCase(parent::relationsToArray());
    }

    public function getHidden(): array
    {
        return array_map(Str::class . '::snake', $this->hidden);
    }

    public function getDates(): array
    {
        $dates = parent::getDates();
        return array_map(Str::class . '::snake', $dates);
    }

    public function toCamelCase($attributes): array
    {
        $convertedAttributes = [];

        foreach ($attributes as $key => $value) {
            $key = $this->getTrueKey($key);
            $convertedAttributes[$key] = $value;
        }

        return $convertedAttributes;
    }

    public function toSnakeCase($attributes): array
    {
        $convertedAttributes = [];

        foreach ($attributes as $key => $value) {
            $convertedAttributes[$this->getSnakeKey($key)] = $value;
        }

        return $convertedAttributes;
    }

    public function getTrueKey($key): string
    {

        if ($this->isCamelCase() && !str_contains($key, 'pivot_')) {
            $key = Str::camel($key);
        }

        return $key;
    }

    public function isCamelCase(): bool
    {
        return $this->enforceCamelCase or (isset($this->parent) && method_exists($this->parent, 'isCamelCase') && $this->parent->isCamelCase());
    }

    protected function getSnakeKey($key): string
    {
        return Str::snake($key);
    }

    public function __isset($key)
    {
        return parent::__isset($key) || parent::__isset($this->getSnakeKey($key));
    }

}
