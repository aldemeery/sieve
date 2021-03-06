<?php

namespace Aldemeery\Sieve;

use Aldemeery\Sieve\Exceptions\UnresolvableFilterException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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
    protected $filters = [
        // Silence is golden...
    ];

    /**
     * Constructor.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder Eloquent builder instance.
     * @param \Illuminate\Http\Request $request Incoming request instance.
     */
    public function __construct(Builder $builder, Request $request)
    {
        $this->builder = $builder;
        $this->request = $request;
    }

    /**
     * Add filters to the engine.
     *
     * @param array $filters Array of filters to add to the engine. Structure: ["filter-name" => FilterClass::class]
     *
     * @return FiltrationEngine
     */
    public function plugFilters(array $filters = [])
    {
        $this->filters = array_merge($this->filters, $filters);

        return $this;
    }

    /**
     * Apply the filters on the builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder Eloquent builder.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->relevantFilters() as $filterName => $value) {
            $filter = $this->resolveFilter($filterName);
            $value = $filter->getMappings()[$value] ?? $value;
            $filter->filter($this->builder, $value);
        }
    }

    /**
     * Get a new filter instance using a given filter name.
     *
     * @param string $filter The filter name.
     *
     * @return \Aldemeery\Sieve\Filter
     */
    public function resolveFilter($filter)
    {
        if (!isset($this->filters[$filter])) {
            throw new UnresolvableFilterException("Could not resolve filter associated with name: '{$filter}'");
        }

        return new $this->filters[$filter];
    }

    /**
     * Extract the relevant (key, value) pairs from the query string based on the
     * filters in this filtration engine instance.
     *
     * @return array
     */
    protected function relevantFilters()
    {
        return Arr::only($this->request->query(), array_keys($this->filters));
    }
}
