<?php

namespace Sasin91\LaravelInteractions;

use Illuminate\Support\ServiceProvider;
use Sasin91\LaravelInteractions\Console\Commands\InteractionMakeCommand;

class InteractionServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([InteractionMakeCommand::class]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            InteractionMakeCommand::class
        ];
    }
}