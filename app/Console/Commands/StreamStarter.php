<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Subscribe;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stream;
use App\Models\StreamSettings;

use App\Models\CronLogs;

class StreamStarter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stream:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для запуска крона Создания Стримов ВК';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $handles = [];
        $allStreams = Stream::where('active', 1)->where('live', 0)->get();
        foreach ($allStreams as $stream) {
            $currentDomain = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
            $streamSettings = StreamSettings::where('stream_id', $stream->id)->first();
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stream->updated_at, 'Europe/Moscow')->startOfMinute();
            $nowTime = Carbon::now('Europe/Moscow')->startOfMinute();
            $accountId = $stream->account_id;
            $groupId = $stream->group_id;
            $vkAccount = VkAccount::where('id', '=', $accountId)->first();
            $ownerId = preg_replace("/[^0-9]/", "", $vkAccount->link);
            $vkGroup = ($groupId > 0) ? VkGroup::where('group_id', '=', $groupId)->first() : null;
            $userId = $vkAccount->uid;
            $vkToken = $vkAccount->token;
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
                        $subscribePackage = unserialize($userSubscribe->sub_config)['vk']['streams'];
                        if($subscribePackage['type1'] === 1){
                            if($vkGroup && $subscribePackage['type2'] !== 1){
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'streams',
                                    'item_id' => $stream->id,
                                    'message'   => 'Пользователь не имеет Streams type2'
                                ]);
                                die();
                            }
                            $streamDescription = $stream->description .'\nТрансляция воспроизводится F-AUTOMATION: '.$currentDomain.'partner='.$userDB->login;
                            $streamContinue = route('streamContinue');
                            $postContent = [
                                'stream_id' => $stream->id,
                                'user_id' => $userId,
                                'token' => $vkToken,
                                'description' => $streamDescription,
                            ];
                            if($subscribePackage['type3'] === 1){
                                $postContent['bitrate'] = '3000k';
                            }else{
                                $postContent['bitrate'] = '2000k';
                            }
                            $response = $client->post($streamContinue, [
                                'form_params' => $postContent,
                            ]);
                            $handles[] = $response;
                        }else{
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'streams',
                                'item_id' => $stream->id,
                                'message'   => 'Пользователь не имеет Streams type1'
                            ]);
                            die();
                        }
                    }else{
                        CronLogs::create([
                            'uid'        => $userId,
                            'cron_type' => 'streams',
                            'item_id' => $stream->id,
                            'message'   => 'Пользователь не имеет активной Подписки'
                        ]);
                        $stream->active = 0;
                        $stream->save();
                    }
                }else{
                    CronLogs::create([
                        'uid'        => $userId,
                        'cron_type' => 'streams',
                        'item_id' => $stream->id,
                        'message'   => 'Пользователь не имеет Подписки в базе данных'
                    ]);
                }
            }
        }
    }
}
