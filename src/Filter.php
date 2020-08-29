<?php

namespace Aldemeery\Sieve;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Values mappings.
     *
     * @var array
     */
    protected $mappings = [
        // Silence is golden...
    ];

    /**
     * Filter records based on a given value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder Eloquent builder instance.
     * @param string $value The resolved value of the filtration key sent in the query string.
     *
     * @return void
     */
    abstract public function filter(Builder $builder, $value);

    /**
     * Get the mappings array.
     *
     * @return array
     */
    public function getMappings()
    {
        return $this->mappings;
    }
}
