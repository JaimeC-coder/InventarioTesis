<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Inventorie::observe(\App\Observers\InventorieObserver::class);
        \App\Models\Purchase::observe(\App\Observers\PurchaseObserver::class);
    }
}
