<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\NewMarkets::class,
        \App\Console\Commands\NewBfSession::class,
        \App\Console\Commands\BfUpdateBooks::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('get:newMarket')->hourly();
        $schedule->command('bf:updateBooks')->everyMinute();
        $schedule->command('bf:newSession')->cron('0 */2 * * *'); // every 2 hours
    }
}
