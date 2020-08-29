<?php

namespace Aldemeery\Sieve\Concerns;

use Aldemeery\Sieve\DefaultFilterBag;
use Aldemeery\Sieve\FiltrationEngine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Scope a query to use filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder Eloquent builder isntance.
     * @param \Illuminate\Http\Request $request Incoming request instance.
     * @param array $filters Array of filters to use. Structure: ["filter-name" => FilterClass::class]
     *
     * @return void
     */
    public function scopeFilter(Builder $builder, Request $request, array $filters = [])
    {
        $filters = array_merge($this->allFilters(), $filters);

        (new FiltrationEngine($builder, $request))->plugFilters($filters)->run();
    }

    /**
     * Filter bags used by the model.
     *
     * @return array
     */
    protected function filterBags()
    {
        return [
            // \FilterBagClass::class
        ];
    }

    /**
     * List of individual filters to be used by the model.
     *
     * @return array
     */
    protected function filters()
    {
        return [
            // "filter-name" => \FilterCalss::class
        ];
    }

    /**
     * Get all filters in the model combined with the filters from the filter bags.
     *
     * @return array
     */
    private function allFilters()
    {
        $filters = $this->filters();

        foreach ($this->filterBags() as $bag) {
            $filters = array_merge($bag::getFilters(), $filters);
        }

        return $filters;
    }
}
