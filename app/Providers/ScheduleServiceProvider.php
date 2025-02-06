<?php

namespace App\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(Schedule $schedule, Application $app): void
    {
        $schedule->command('rabbitmq:consume')
            ->everyFiveSeconds()
            ->withoutOverlapping();
    }
}
