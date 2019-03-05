<?php

namespace Aldemeery\Sieve\Commands;

use Illuminate\Console\GeneratorCommand;

class FilterBagMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:filter-bag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new filter bag class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Filter Bag';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/filter-bag.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * 
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Filters';
    }
}
