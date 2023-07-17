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
        $nowTime = Carbon::now()->timestamp;
        $allStories = Stories::where('active', 0)->get();
        foreach ($allStories as $stories) {
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stories->updated_at, 'Europe/Moscow')->startOfMinute()->addDays(7);
            $updatedAtTimestamp = strtotime($updatedAt->toDateTimeString());
            if($nowTime > $updatedAtTimestamp){
                $removeId = $stories->id;
                StoriesStory::where('stories_id', '=', $removeId)->delete();
                StoriesSettings::where('stories_id', '=', $removeId)->delete();
                $files = unserialize($stories->link);
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
                Stories::where('id', '=', $removeId)->delete();
            }
        }
        $allStreams = Stream::where('active', 0)->get();
        foreach ($allStreams as $stream) {
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stream->updated_at, 'Europe/Moscow')->startOfMinute()->addDays(7);
            $updatedAtTimestamp = strtotime($updatedAt->toDateTimeString());
            if($nowTime > $updatedAtTimestamp){
                $removeId = $stream->id;
                StreamStory::where('stream_id', '=', $removeId)->delete();
                StreamComments::where('stream_id', '=', $removeId)->delete();
                StreamSettings::where('stream_id', '=', $removeId)->delete();
                $files = unserialize($stream->link);
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
                Stream::where('id', '=', $removeId)->delete();
            }
        }
        $allPosts = Post::where('active', 0)->get();
        foreach ($allPosts as $post) {
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $post->updated_at, 'Europe/Moscow')->startOfMinute()->addDays(7);
            $updatedAtTimestamp = strtotime($updatedAt->toDateTimeString());
            if($nowTime > $updatedAtTimestamp){
                $removeId = $post->id;
                PostSettings::where('post_id', '=', $removeId)->delete();
                PostStory::where('post_id', '=', $removeId)->delete();
                $files = unserialize($post->link);
                $attachments = unserialize($post->attachments);
                foreach ($files as $key => $file) {
                    if((rtrim(strstr($attachments[$key], '_', true), "0123456789") == 'video')){
                        Storage::disk('public')->delete($file);
                    }
                }
                Post::where('id', '=', $removeId)->delete();
            }
        }
        $tgStreams = TgStream::where('active', 0)->get();
        foreach ($tgStreams as $stream) {
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stream->updated_at, 'Europe/Moscow')->startOfMinute()->addDays(7);
            $updatedAtTimestamp = strtotime($updatedAt->toDateTimeString());
            if($nowTime > $updatedAtTimestamp){
                $removeId = $stream->id;
                TgStreamSettings::where('stream_id', '=', $removeId)->delete();
                $files = unserialize($stream->link);
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
                TgStream::where('id', '=', $removeId)->delete();
            }
        }
        $tgPosts = TgPost::where('active', 0)->get();
        foreach ($tgPosts as $post) {
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $post->updated_at, 'Europe/Moscow')->startOfMinute()->addDays(7);
            $updatedAtTimestamp = strtotime($updatedAt->toDateTimeString());
            if($nowTime > $updatedAtTimestamp){
                $removeId = $post->id;
                TgPostSettings::where('post_id', '=', $removeId)->delete();
                $files = unserialize($post->link);
                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }
                TgPost::where('id', '=', $removeId)->delete();
            }
        }
    }
}
