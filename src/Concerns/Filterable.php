<?php

declare(strict_types=1);

namespace Aldemeery\Sieve\Concerns;

use Aldemeery\Onion;
use Aldemeery\Sieve\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use RuntimeException;

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
                function (string $filter): Filter {
                    $filter = App::make($filter);

                    if (!$filter instanceof Filter) {
                        throw new RuntimeException(sprintf(
                            'Filters must implement %s, but %s does not.',
                            Filter::class,
                            is_object($filter) ? get_class($filter) : gettype($filter),
                        ));
                    }

                    return $filter;
                },
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
