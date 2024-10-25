<?php

declare(strict_types=1);

namespace Tests\Aldemeery\Sieve;

use Aldemeery\Sieve\Concerns\Filterable;
use Illuminate\Database\Eloquent\Model;

/** @method \Illuminate\Database\Eloquent\Builder filter(array $params = [], array $additional = []) */
class ModelWithFilters extends Model
{
    use Filterable;

    protected $table = 'tests';

    /** @return array<string, string> */
    private function filters(): array
    {
        return [
            'test-1' => TestFilter::class,
            'test-2' => TestFilter::class,
        ];
    }
}
