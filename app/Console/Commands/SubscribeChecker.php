<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Models\Subscribe;

use App\Models\VkAccount;

use App\Models\Stories;

use App\Models\Stream;

use App\Models\Post;

use App\Models\TgChannel;

use App\Models\TgStream;

use App\Models\TgPost;

class SubscribeChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribe:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка и остановка подписок';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $nowTime = Carbon::now()->timestamp;
        $allSubscribe = Subscribe::where('sub_status', 1)->get();
        foreach ($allSubscribe as $subscribe) {
            $userId = $subscribe->uid;
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $subscribe->end_date, 'Europe/Moscow');
            $endDateTimestamp = strtotime($endDate->toDateTimeString());
            if($nowTime >= $endDateTimestamp){
                $vkAccounts = VkAccount::where('uid', $userId)->get();
                foreach ($vkAccounts as $account) {
                    $allStories = Stories::where('account_id', $account->id)->where('active', 1)->get();
                    foreach ($allStories as $stories) {
                        $stories->active = 0;
                        $stories->save();
                    }
                    $allStreams = Stream::where('account_id', $account->id)->where('active', 1)->get();
                    foreach ($allStreams as $stream) {
                        $stream->active = 0;
                        $stream->save();
                    }
                    $allPosts = Post::where('account_id', $account->id)->where('active', 1)->get();
                    foreach ($allPosts as $post) {
                        $post->active = 0;
                        $post->save();
                    }
                }
                $tgChannels = TgChannel::where('uid', $userId)->get();
                foreach ($tgChannels as $channel) {
                    $tgStreams = TgStream::where('channel_id', $channel->id)->where('active', 1)->get();
                    foreach ($tgStreams as $stream) {
                        $stream->active = 0;
                        $stream->save();
                    }
                    $tgPosts = TgPost::where('channel_id', $channel->id)->where('active', 1)->get();
                    foreach ($tgPosts as $post) {
                        $post->active = 0;
                        $post->save();
                    }
                }
                $subscribe->sub_status = 0;
                $subscribe->is_test = 0;
                $subscribe->save();
            }
        }
    }
}
