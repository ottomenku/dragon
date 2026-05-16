<?php

namespace App\Providers;

use App\Models\WebshopSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('*', function ($view) {
            $user = auth()->user();
            $view->with('webshopLinkVisible', WebshopSetting::isOpen() || ($user && $user->isAdmin()));
        });
    }
}
