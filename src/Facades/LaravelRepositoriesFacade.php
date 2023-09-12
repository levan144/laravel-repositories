<?php

namespace Levan144\LaravelRepositories\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelRepositoriesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-repositories';
    }
}