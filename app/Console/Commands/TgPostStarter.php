<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Subscribe;

use App\Models\TgChannel;

use App\Models\TgPost;
use App\Models\TgPostSettings;

use App\Models\CronLogs;

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
        $start = microtime(true);
        $promises = [];
        $mh = curl_multi_init();
        $currentDomain = env('APP_SITE_LINK');

        $allPosts = TgPost::where('active', 1)->get();
        foreach ($allPosts as $post) {
            $postSettings = TgPostSettings::where('post_id', $post->id)->first();
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $post->updated_at, 'Europe/Moscow')->startOfMinute();
            $nowTime = Carbon::now('Europe/Moscow')->startOfMinute();
            $channelId = $post->channel_id;
            $tgChannel = TgChannel::where('id', '=', $channelId)->first();
            $userId = $tgChannel->uid;
            if($postSettings->type === 'date'){
                $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $postSettings->date, 'Europe/Moscow')->startOfMinute();
            }elseif ($postSettings->type === 'interval') {
                $updatedAt->addMinutes($postSettings->interval);
            }else{
                $updatedAt = Carbon::now('Europe/Moscow')->startOfMinute()->setTime($postSettings->daily, 0, 0);
            }
            $userDB = User::where('id', '=', $userId)->first();
            if (strtotime($updatedAt) === strtotime($nowTime)) {
                $userSubscribe = Subscribe::where('uid', '=', $userId)->first();
                if($userSubscribe){
                    if($userSubscribe->sub_status === 1){
                        $subscribePackage = unserialize($userSubscribe->sub_config)['tg']['posts'];
                        if($subscribePackage['type1'] === 1){
                            $postContinue = $currentDomain.'/tg-post-continue';
                            $postContent = [
                                'post_id' => $post->id,
                                'user_id' => $userId,
                                'username' => $userDB->login,
                                'channel' => $tgChannel->name,
                            ];
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $postContinue);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_multi_add_handle($mh, $ch);
                            $promises[] = $ch;
                        }else{
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'tg_posts',
                                'item_id' => $post->id,
                                'message'   => 'Пользователь не имеет Posts type1'
                            ]);
                            die();
                        }
                    }else{
                        CronLogs::create([
                            'uid'        => $userId,
                            'cron_type' => 'tg_posts',
                            'item_id' => $post->id,
                            'message'   => 'Пользователь не имеет активной Подписки'
                        ]);
                        $post->active = 0;
                        $post->save();
                    }
                }else{
                    CronLogs::create([
                        'uid'        => $userId,
                        'cron_type' => 'tg_posts',
                        'item_id' => $post->id,
                        'message'   => 'Пользователь не имеет Подписки в базе данных'
                    ]);
                }
            }elseif (strtotime($updatedAt->subMinutes(5)) === strtotime($nowTime)) {
                if($postSettings->type !== 'date'){
                    $postContinue = $currentDomain.'/tg-post-remover';
                    $postContent = [
                        'post_id' => $post->id,
                        'user_id' => $userId,
                        'username' => $userDB->login,
                        'channel' => $tgChannel->name,
                    ];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $postContinue);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_multi_add_handle($mh, $ch);
                    $promises[] = $ch;
                }
            }
        }
        $isRunning = null;
        do {
            curl_multi_exec($mh, $isRunning);
            sleep(1);
        } while ($isRunning > 0);
        $outputs = array();
        for ($i = 0; $i < count($promises); $i++) {
            $outputs[$i] = trim(curl_multi_getcontent($promises[$i]));
            curl_multi_remove_handle($mh, $promises[$i]);
        }
        curl_multi_close($mh);
    }
}
