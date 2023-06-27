<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use Carbon\Carbon;
use CURLFile;

use App\Models\Subscribe;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Post;
use App\Models\PostSettings;
use App\Models\PostStory;

use App\Models\CronLogs;

class PostStarter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для запуска крона Создания Постов ВК';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $allPosts = Post::where('active', 1)->get();
        foreach ($allPosts as $post) {
            $postSettings = PostSettings::where('post_id', $post->id);
            $startType = $postSettings->type;
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $post->updated_at, 'Europe/Moscow')->startOfMinute();

            $accountId = $post->account_id;
            $groupId = $post->group_id;
            $vkAccount = VkAccount::where('id', '=', $accountId)->first();
            $userId = $vkAccount->uid;
            $vkToken = $vkAccount->token;
            if($startType === 'date'){
                $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $postSettings->date, 'Europe/Moscow')->startOfMinute();
            }elseif ($type === 'interval') {
                $updatedAt->addMinutes($postSettings->interval);
            }else{
                $updatedAt->setTime($postSettings->daily, 0, 0);
            }
            if (strtotime($updatedAt) === strtotime(Carbon::now('Europe/Moscow')->startOfMinute())) {
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'posts',
                    'item_id' => $post->id,
                    'message'   => 'Время совпало!'
                ]);
                $vkGroup = ($groupId > 0) ? VkGroup::where('group_id', '=', $groupId)->first() : null;
                $userSubscribe = Subscribe::where('uid', '=', $userId)->first();
                if($userSubscribe){
                    if($userSubscribe->sub_status === 1){
                        $subscribePackage = unserialize($userSubscribe->sub_config)['vk']['posts'];
                        if($subscribePackage['type1'] === 1){
                            if($vkGroup && $subscribePackage['type2'] !== 1){
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'posts',
                                    'item_id' => $post->id,
                                    'message'   => 'Пользователь не имеет Posts type2'
                                ]);
                                die();
                            }
                            if(count(unserialize($post->attachments)) > 1 && $subscribePackage['type3'] !== 1){
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'posts',
                                    'item_id' => $post->id,
                                    'message'   => 'Пользователь не имеет Posts type3'
                                ]);
                                die();
                            }
                        }else{
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'posts',
                                'item_id' => $post->id,
                                'message'   => 'Пользователь не имеет Posts type1'
                            ]);
                            die();
                        }
                    }else{
                        CronLogs::create([
                            'uid'        => $userId,
                            'cron_type' => 'posts',
                            'item_id' => $post->id,
                            'message'   => 'Пользователь не имеет активной Подписки'
                        ]);
                        $post->active = 0;
                        $post->save();
                    }
                }else{
                    CronLogs::create([
                        'uid'        => $userId,
                        'cron_type' => 'posts',
                        'item_id' => $post->id,
                        'message'   => 'Пользователь не имеет Подписки в базе данных'
                    ]);
                }
            }
        }
    }
}
