<?php

namespace Aldemeery\Sieve\Concerns;

use Aldemeery\Sieve\DefaultFilterBag;
use Aldemeery\Sieve\Exceptions\FilterBagNotFoundException;
use Aldemeery\Sieve\FiltrationEngine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    /**
     * Scope a query to use filters.
     *
     * @param Builder $builder
     * @param Request $request
     * @param array   $filters
     *
     * @return Builder
     */
    public function scopeFilter(Builder $builder, Request $request, array $filters = [])
    {
        $filters = array_merge($this->allFilters(), $filters);

        return (new FiltrationEngine($builder, $request))->plugFilters($filters)->run();
    }

    /**
     * Filter bags used by the model.
     *
     * @return string
     */
    protected function filterBags()
    {
        return [
            // 
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
            //
        ];
    }

    /**
     * Get all filters in the model combined.
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
