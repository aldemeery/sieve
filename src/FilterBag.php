<?php

namespace Aldemeery\Sieve;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class FilterBag
{
    /**
     * Filters to be applied.
     * 
     * @var array
     */
    protected static $filters = [
        // 
    ];

    /**
     * Get filters in bag.
     * 
     * @return array 
     */
    public static function getFilters()
    {
        return static::$filters;
    }
}