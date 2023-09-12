<?php

namespace App\Providers;
use App\Repositories\Volume\VolumeRepositoryInterface;
use App\Repositories\Volume\VolumeRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Role\RoleRepository;
use App\Repositories\ProjectUserRole\ProjectUserRoleRepositoryInterface;
use App\Repositories\ProjectUserRole\ProjectUserRoleRepository;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Plan\PlanRepositoryInterface;
use App\Repositories\Plan\PlanRepository;
use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Project\ProjectRepository;
use App\Repositories\Project\ProjectRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        /** Start of Repositories */
        
        /** End of Repositories */
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
