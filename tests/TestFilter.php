<?php

declare(strict_types=1);

namespace Tests\Aldemeery\Sieve;

use Aldemeery\Sieve\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

/** @implements Filter<ModelWithFilters> */
class TestFilter implements Filter
{
    public function map(mixed $value): mixed
    {
        return match ($value) {
            default => $value,
        };
    }

    public function apply(Builder $query, mixed $value): void
    {
        $query->where('test', $value);
    }
}
