<?php

namespace App\Models;

class AppPaginate
{
    public int $first;
    public int $previous;
    public int $current;
    public int $next;
    public int $last;
    public int $total;
    public int $perPage;
    public array $position;

    public function __construct(int $first, int $previous, int $current, int $next, int $last, int $total, int $perPage, array $position)
    {
        $this->first = $first;
        $this->previous = $previous;
        $this->current = $current;
        $this->next = $next;
        $this->last = $last;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->position = $position;
    }

    public static function create(int $first, int $previous, int $current, int $next, int $last, int $total = 0, int $perPage = 0, $position = []): AppPaginate
    {
        return new self(
            first: $first,
            previous: $previous,
            current: $current,
            next: $next,
            last: $last,
            total: $total,
            perPage: $perPage,
            position: $position
        );
    }
}
