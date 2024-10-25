<?php

declare(strict_types=1);

namespace Tests\Aldemeery\Sieve;

use Illuminate\Database\Eloquent\Builder;

class BadFilter
{
    public function map(mixed $value): mixed
    {
        return match ($value) {
            default => $value,
        };
    }

    /** @param Builder<ModelWithoutFilters> $query */
    public function apply(Builder $query, mixed $value): void
    {
        $query->where('test', $value);
    }
}
