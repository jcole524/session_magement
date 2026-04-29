<?php

namespace App\Providers;

use App\Models\ProgressLog;
use App\Models\WorkoutSession;
use App\Observers\ProgressLogObserver;
use App\Observers\SessionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        WorkoutSession::observe(SessionObserver::class);
        ProgressLog::observe(ProgressLogObserver::class);
    }
}