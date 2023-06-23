<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CronLogs;

class StoriesStarter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stories:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для запуска крона Создания Историй ВК';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");
        CronLogs::create([
            'message'   => 'Крона сработала!'
        ]);
    }
}
