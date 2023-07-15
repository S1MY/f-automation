<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Subscribe;

use App\Models\TgChannel;

use App\Models\TgStream;
use App\Models\TgStreamSettings;

use App\Models\CronLogs;

class TgStreamStarter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tg_stream:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для запуска крона Создания Стримов ТГ';

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

        $allStreams = TgStream::where('active', 1)->where('live', 0)->get();
        foreach ($allStreams as $stream) {
            $currentDomain = 'https://f-automation.v-devo.ru';
            $streamSettings = TgStreamSettings::where('stream_id', $stream->id)->first();
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stream->updated_at, 'Europe/Moscow')->startOfMinute();
            $nowTime = Carbon::now('Europe/Moscow')->startOfMinute();
            $channelId = $stream->channel_id;
            $tgChannel = TgChannel::where('id', '=', $channelId)->first();
            $userId = $tgChannel->uid;
            if($streamSettings->type === 'date'){
                $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $streamSettings->date, 'Europe/Moscow')->startOfMinute();
            }elseif ($streamSettings->type === 'interval') {
                $updatedAt->addMinutes($streamSettings->interval);
            }else{
                $updatedAt = Carbon::now('Europe/Moscow')->startOfMinute()->setTime($streamSettings->daily, 0, 0);
            }
            if (strtotime($updatedAt) === strtotime($nowTime)) {
                $userSubscribe = Subscribe::where('uid', '=', $userId)->first();
                $userDB = User::where('id', '=', $userId)->first();
                if($userSubscribe){
                    if($userSubscribe->sub_status === 1){
                        $subscribePackage = unserialize($userSubscribe->sub_config)['tg']['streams'];
                        if($subscribePackage['type1'] === 1){
                            $streamContinue = $currentDomain.'/tg-stream-continue';
                            $postContent = [
                                'stream_id' => $stream->id,
                                'user_id' => $userId,
                                'username' => $userDB->login,
                                'channel' => $tgChannel->name,
                            ];
                            if($subscribePackage['type2'] === 1){
                                $postContent['bitrate'] = '4096k';
                            }else{
                                $postContent['bitrate'] = '2048k';
                            }
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $streamContinue);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_multi_add_handle($mh, $ch);
                            $promises[] = $ch;
                        }else{
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'tg_streams',
                                'item_id' => $stream->id,
                                'message'   => 'Пользователь не имеет Streams type1'
                            ]);
                            die();
                        }
                    }else{
                        CronLogs::create([
                            'uid'        => $userId,
                            'cron_type' => 'tg_streams',
                            'item_id' => $stream->id,
                            'message'   => 'Пользователь не имеет активной Подписки'
                        ]);
                        $stream->active = 0;
                        $stream->save();
                    }
                }else{
                    CronLogs::create([
                        'uid'        => $userId,
                        'cron_type' => 'tg_streams',
                        'item_id' => $stream->id,
                        'message'   => 'Пользователь не имеет Подписки в базе данных'
                    ]);
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
