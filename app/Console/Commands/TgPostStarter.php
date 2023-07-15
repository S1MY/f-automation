<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TgPostStarter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tg_post:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для запуска крона Создания Постов ТГ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
