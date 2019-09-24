<?php

namespace Callscripts;

use Illuminate\Support\ServiceProvider;

class CallscriptsServiceProvider extends ServiceProvider
{

    protected $defer = false;
    protected $namespace = 'Callscripts\Callscripts';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Callscripts\Callscripts\HomeController');
        $this->app->make('Callscripts\Callscripts\ManagementController');
        $this->loadViewsFrom(__DIR__.'/views', 'callscripts');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->routesAreCached()) {
            $this->loadRoutesFrom(__DIR__.'/routes.php');
            //require __DIR__.'/routes.php';
        }
    }
}
