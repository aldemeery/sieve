<?php

namespace Aldemeery\Sieve;

use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Filter values mappings.
     * 
     * @var array
     */
    protected $mappings = [];

    /**
     * Filter records.
     * 
     * @param Builder $builder 
     * @param mixed   $value   
     * 
     * @return Builder
     */
    abstract public function filter(Builder $builder, $value);

    /**
     * Resolve mapping value by key
     * 
     * @param mixed $key 
     * 
     * @return mixed|null
     */
    protected function resolveValue($key)
    {
        return array_get($this->mappings, $key);
    }
}