<?php

declare(strict_types=1);

namespace Aldemeery\Sieve;

use Aldemeery\Sieve\Commands\FilterMakeCommand;
use Illuminate\Support\ServiceProvider;

class SieveServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FilterMakeCommand::class,
            ]);
        }
    }
}
