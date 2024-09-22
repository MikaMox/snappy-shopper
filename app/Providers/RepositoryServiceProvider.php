<?php

namespace App\Providers;

use App\Interfaces\PostcodeRepositoryInterface;
use App\Interfaces\ShopRepositoryInterface;
use App\Repositories\PostcodeRepository;
use App\Repositories\ShopRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PostcodeRepositoryInterface::class, PostcodeRepository::class);
        $this->app->bind(ShopRepositoryInterface::class, ShopRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
