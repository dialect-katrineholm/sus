<?php

namespace Dialect\Sus;

use Illuminate\Support\ServiceProvider;

class SusServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
	    $this->app->bind('sus', Sus::class);
    }
}