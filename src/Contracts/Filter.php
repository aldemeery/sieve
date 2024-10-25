<?php

declare(strict_types=1);

namespace Aldemeery\Sieve\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** @template Filterable of Model */
interface Filter
{
    public function map(mixed $value): mixed;

    /** @param Builder<Filterable> $query */
    public function apply(Builder $query, mixed $value): void;
}
