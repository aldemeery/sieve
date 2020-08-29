<?php

namespace Aldemeery\Sieve\Tests;

use Aldemeery\Sieve\Exceptions\UnresolvableFilterException;
use Aldemeery\Sieve\Filter;
use Aldemeery\Sieve\FiltrationEngine;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class FiltrationEngineTest extends TestCase
{
    private $builder;

    private $request;

    private $engine;

    public function setUp(): void
    {
        $this->builder = $this->getBuilder();
        $this->request = Request::create('?color=red', 'GET');
        $this->engine = new FiltrationEngine($this->builder, $this->request);
    }

    public function testPluggingFilters()
    {
        $reflection = new ReflectionObject($this->engine);
        $property = $reflection->getProperty('filters');
        $property->setAccessible(true);
        $this->engine->plugFilters([
            'color' => ColorFilter::class,
        ]);

        $this->assertEquals($property->getValue($this->engine), [
            'color' => ColorFilter::class,
        ]);
    }

    public function testResolvingFilters()
    {
        $this->engine->plugFilters([
            'color' => ColorFilter::class,
        ]);

        $filter = $this->engine->resolveFilter('color');
        $this->assertInstanceOf(ColorFilter::class, $filter);

        $this->expectException(UnresolvableFilterException::class);
        $this->engine->resolveFilter('not-found');
    }

    public function testRunningFiltrationEngine()
    {
        $this->engine->run();
        $this->assertEquals('select *', $this->builder->toSql());

        $this->engine->plugFilters([
            'color' => ColorFilter::class,
        ]);
        $this->engine->run();
        $this->assertEquals('select * where "color" = ?', $this->builder->toSql());
        $this->assertEquals(["red"], $this->builder->getBindings());
    }

    public function testAutomaticMappingResolve()
    {
        $this->engine->plugFilters([
            'color' => ColorFilter::class,
        ]);

        $this->request->merge(['color' => 'main']);
        $this->engine->run();
        $this->assertEquals(["red"], $this->builder->getBindings());
    }

    private function getBuilder()
    {
        return new Builder(
            new BaseBuilder(
                $this->createMock(ConnectionInterface::class),
                new Grammar(),
                new Processor()
            )
        );
    }
}

class ColorFilter extends Filter
{
    protected $mappings = [
        'main' => 'red',
    ];

    public function filter(Builder $builder, $value)
    {
        $builder->where('color', $value);
    }
}
