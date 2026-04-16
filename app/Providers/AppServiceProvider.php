<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        Gate::policy(\App\Models\Chat::class, \App\Policies\ChatPolicy::class);
    Gate::policy(\App\Models\Document::class, \App\Policies\DocumentPolicy::class);
    }
}
