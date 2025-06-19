<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Timetable;
use App\Observers\TimetableObserver;

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
        Timetable::observe(TimetableObserver::class);
    }
}
