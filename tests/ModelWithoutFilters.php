<?php

declare(strict_types=1);

namespace Tests\Aldemeery\Sieve;

use Aldemeery\Sieve\Concerns\Filterable;
use Illuminate\Database\Eloquent\Model;

/** @method \Illuminate\Database\Eloquent\Builder filter(array $params = [], array $additional = []) */
class ModelWithoutFilters extends Model
{
    use Filterable;

    protected $table = 'tests';
}
