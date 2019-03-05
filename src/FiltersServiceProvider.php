<?php

namespace Aldemeery\Sieve;

use Aldemeery\Sieve\Commands\FilterBagMakeCommand;
use Aldemeery\Sieve\Commands\FilterMakeCommand;
use Illuminate\Support\ServiceProvider;

class FiltersServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilterMakeCommand::class,
                FilterBagMakeCommand::class,
            ]);
        }
    }
}
