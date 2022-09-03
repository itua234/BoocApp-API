<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Interfaces\IProfileInterface',
            'App\Repositories\ProfileRepository'
        );
        
        $this->app->bind(
            'App\Interfaces\IArtisanInterface',
            'App\Repositories\ArtisanRepository'
        );

        $this->app->bind(
            'App\Interfaces\IUserInterface',
            'App\Repositories\UserRepository'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
