<?php

namespace Aldemeery\Sieve\Tests;

use Aldemeery\Sieve\Filter;
use Aldemeery\Sieve\FilterBag;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\TestCase;

class FilterBagTest extends TestCase
{
    public function testGettingFilterBagFilters()
    {
        $concrete = new class extends FilterBag {
            protected static $filters = [
                Filter::class,
            ];
        };

        $this->assertEquals($concrete::getFilters(), [
            Filter::class,
        ]);
    }
}

class TestFilter extends Filter
{
    public function filter(Builder $builder, $value) {}
}
