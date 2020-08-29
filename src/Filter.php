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
     * @param string $value The value of the filtration key sent with the request.
     *
     * @return void
     */
    abstract public function filter(Builder $builder, $value);

    /**
     * Get the mappings array.
     *
     * @return array The mappings array
     */
    public function getMappings()
    {
        return $this->mappings;
    }
}
