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
        \App\Console\Commands\HarvestYoutube::class,
        \App\Console\Commands\GenerateFeed::class,
        \App\Console\Commands\GenerateVortexHtml::class,
        \App\Console\Commands\PubSub::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('harvest:youtube')
            ->twiceDaily(13, 16)
            ->emailOutputTo('d.m.heggo@ub.uio.no');

        $schedule->command('generate:vortex')
            ->everyThirtyMinutes()
            ->weekdays()
            ->between('12:00', '20:00');

        // The pubsubhubbub subscriptions lasts 5 days, so we need to re-new
        // them at latest every 5th day.
        $schedule->command('pubsub:subscribe')
            ->daily();
    }
}
