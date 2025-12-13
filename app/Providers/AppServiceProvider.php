<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
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
        // Register Blade directive for checking approval access
        Blade::if('canApproveReject', function () {
            return \App\Helpers\AuthHelper::canApproveReject();
        });

        // Register Blade directive for checking if user is ASMAN KSPI
        Blade::if('isAsmanKspi', function () {
            return \App\Helpers\AuthHelper::isAsmanKspi();
        });

        // Register Blade directive for checking if user is KSPI
        Blade::if('isKspi', function () {
            return \App\Helpers\AuthHelper::isKspi();
        });

        // Register Blade directive for checking if user is BOD
        Blade::if('isBod', function () {
            return \App\Helpers\AuthHelper::isBod();
        });

        // Register Blade directive for checking if user can modify data (create, edit, delete)
        Blade::if('canModifyData', function () {
            return \App\Helpers\AuthHelper::canModifyData();
        });
    }
}
