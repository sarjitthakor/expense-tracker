<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Events\ExpenseCreated;
use App\Listeners\CheckBudgetAlert;

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
        // 💰 Register budget alert listener
        $this->app['events']->listen(
            ExpenseCreated::class,
            CheckBudgetAlert::class
        );
    }
}
