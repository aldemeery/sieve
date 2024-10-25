<?php

declare(strict_types=1);

namespace Aldemeery\Sieve\Concerns;

use Aldemeery\Onion;
use Aldemeery\Sieve\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Psl\Type;

trait Filterable
{
    /**
     * @param Builder<Model>       $query
     * @param array<string, mixed> $params
     * @param array<string, mixed> $additional
     */
    protected function scopeFilter(Builder $query, array $params = [], array $additional = []): void
    {
        Onion\onion([
            fn (array $filters): array => array_merge($filters, $additional),
            fn (array $filters): array => array_intersect_key($filters, $params),
            fn (array $filters): array => array_map(
                fn (string $filter): Filter => Type\instance_of(Filter::class)->assert(App::make($filter)),
                $filters,
            ),
            fn (array $filters): true => array_walk(
                $filters,
                fn (Filter $filter, string $key) => $filter->apply($query, $filter->map($params[$key])),
            ),
        ])->withoutExceptionHandling()->peel($this->filters());
    }

    /** @return array<string, string> */
    private function filters(): array
    {
        return [
            // "filter-name" => \FilterCalss::class
        ];
    }
}
