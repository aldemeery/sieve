<?php

namespace Aldemeery\Sieve;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FiltrationEngine
{
    /**
     * Request instance.
     * 
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Builder instance.
     * 
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * Array of filters to be applied.
     * 
     * @var array
     */
    protected $filters = [];

    /**
     * Constructor.
     * 
     * @param Builder $builder
     * @param Request $request 
     */
    public function __construct(Builder $builder, Request $request)
    {
        $this->builder = $builder;
        $this->request = $request;
    }

    /**
     * Add filters to engine.
     * 
     * @param array $filters 
     *
     * @return $this
     */
    public function plugFilters(array $filters = [])
    {
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    /**
     * Apply filters on query.
     * 
     * @param Builder $builder 
     * 
     * @return Builder
     */
    public function run()
    {
        foreach ($this->getFilters() as $filter => $value) {
            $this->resolveFilter($filter)->filter($this->builder, $value);
        }

        return $this->builder;
    }

    /**
     * Get applicable filters based on their presence in the query string.
     * 
     * @return array 
     */
    protected function getFilters()
    {
        return $this->filterFilters($this->filters);
    }

    /**
     * Resolve a filter from the filters array by its key.
     * 
     * @param mixed $filter
     * 
     * @return \Aldemeery\Sieve\Filter
     */
    public function resolveFilter($filter)
    {
        return new $this->filters[$filter];
    }

    /**
     * Get only the filters included in the query string
     * and return a key, value pair array.
     * 
     * @param array $filters 
     * 
     * @return array
     */ 
    protected function filterFilters(array $filters)
    {
        return array_filter($this->request->only(array_keys($filters)));
    }
}
