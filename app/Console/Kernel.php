<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [

        Commands\StoriesStarter::class,
        Commands\PostStarter::class,
        Commands\StreamStarter::class,
        Commands\TgStreamStarter::class,
        Commands\TgPostStarter::class,
        Commands\FileRemover::class,
        Commands\SubscribeChecker::class,

    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('stories:cron')->everyMinute();
        $schedule->command('post:cron')->everyMinute();
        $schedule->command('stream:cron')->everyMinute();

        $schedule->command('tg_stream:cron')->everyMinute();
        $schedule->command('tg_post:cron')->everyMinute();

        $schedule->command('remover:cron')->everyTwoHours();
        
        $schedule->command('subscribe:cron')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
