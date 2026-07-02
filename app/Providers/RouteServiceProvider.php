<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // register route middleware alias for role checking
        Route::aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);

        // you can load routes here if needed, but the default web.php is already
        // loaded by bootstrap/app.php via withRouting().
    }
}
