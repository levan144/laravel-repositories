<?php

namespace Levan144\LaravelRepositories\Providers;

use Illuminate\Support\ServiceProvider;
use Levan144\LaravelRepositories\Commands\MakeRepositoryCommand;

class LaravelRepositoriesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../Stubs' => resource_path('stubs'),
            ], 'laravel-repositories-stubs');

            
        }
    }

    public function register()
    {
        $this->app->singleton('laravel-repositories', function () {
            return new \Levan144\LaravelRepositories\LaravelRepositories;
        });
    }
}
