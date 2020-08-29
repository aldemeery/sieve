<?php

namespace Aldemeery\Sieve\Tests;

use Aldemeery\Sieve\Filter;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    public function testGettingFilterMappings()
    {
        $concrete = new class extends Filter {
            protected $mappings = [
                'highest' => 'desc',
                'lowest' => 'asc',
            ];

            public function filter(Builder $builder, $value) {}
        };

        $this->assertEquals($concrete->getMappings(), [
            'highest' => 'desc',
            'lowest' => 'asc',
        ]);
    }
}
