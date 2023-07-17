<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\Stories;
use App\Models\StoriesStory;
use App\Models\StoriesSettings;

use App\Models\Stream;
use App\Models\StreamStory;
use App\Models\StreamSettings;
use App\Models\StreamComments;

use App\Models\Post;
use App\Models\PostSettings;
use App\Models\PostStory;

use App\Models\TgStream;
use App\Models\TgStreamSettings;

use App\Models\TgPost;
use App\Models\TgPostSettings;

class FileRemover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remover:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление неактивных файлов спустя неделю';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $allStories = Stories::where('active', 0)->get();
        foreach ($allStories as $stories) {
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stories->updated_at, 'Europe/Moscow')->startOfMinute();
            $updatedAtTimestamp = strtotime($updatedAt->toDateTimeString());
            $updatedAtNext = $updatedAt->copy()->addDay()->startOfMinute();
        }
        // Storage::disk('public')->delete($link);
        return Command::SUCCESS;
    }
}
