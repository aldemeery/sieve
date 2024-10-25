<?php

declare(strict_types=1);

namespace Tests\Aldemeery\Sieve\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use PDO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Aldemeery\Sieve\BadFilter;
use Tests\Aldemeery\Sieve\ModelWithFilters;
use Tests\Aldemeery\Sieve\ModelWithoutFilters;
use Tests\Aldemeery\Sieve\TestFilter;

#[CoversClass(ModelWithFilters::class)]
#[CoversClass(ModelWithoutFilters::class)]
class FilterableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Model::setConnectionResolver(new class () extends ConnectionResolver {
            public function __construct()
            {
                parent::__construct([
                    null => new class () extends Connection {
                        public function __construct()
                        {
                            parent::__construct(new PDO('sqlite::memory:'));
                        }
                    },
                ]);
            }
        });
    }

    public function test_has_no_filters_by_default(): void
    {
        App::shouldReceive('make')->with(TestFilter::class)->andReturn(new TestFilter());

        $filterable = new ModelWithoutFilters();

        $query = $filterable->filter(['test-1' => 'one', 'test-2' => 'two', 'test-3' => 'three']);

        static::assertSame('select * from "tests"', $query->toSql());
        static::assertSame([], $query->getBindings());
    }

    public function test_filters_are_applied(): void
    {
        App::shouldReceive('make')->with(TestFilter::class)->andReturn(new TestFilter());

        $filterable = new ModelWithFilters();

        $query = $filterable->filter(['test-1' => 'one', 'test-3' => 'three']);

        static::assertSame('select * from "tests" where "test" = ?', $query->toSql());
        static::assertSame(['one'], $query->getBindings());
    }

    public function test_additional_filters_are_applied(): void
    {
        App::shouldReceive('make')->with(TestFilter::class)->andReturn(new TestFilter());

        $filterable = new ModelWithFilters();

        $query = $filterable->filter(['test-1' => 'one', 'test-3' => 'three'], ['test-3' => TestFilter::class]);

        static::assertSame('select * from "tests" where "test" = ? and "test" = ?', $query->toSql());
        static::assertSame(['one', 'three'], $query->getBindings());
    }

    public function test_filters_not_implementing_the_filter_interface_throw_an_exception(): void
    {
        App::shouldReceive('make')->with(BadFilter::class)->andReturn(new BadFilter());

        $filterable = new ModelWithoutFilters();

        static::expectException(RuntimeException::class);
        static::expectExceptionMessage('Filters must implement Aldemeery\Sieve\Contracts\Filter, but Tests\Aldemeery\Sieve\BadFilter does not.');

        $filterable->filter(['test-1' => 'one'], ['test-1' => BadFilter::class]);
    }
}
