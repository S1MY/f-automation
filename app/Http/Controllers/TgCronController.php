<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\TgStream;
use App\Models\TgStreamSettings;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings\Logger as LoggerSettings;

use App\Models\CronLogs;

class TgCronController extends Controller
{
    public function tgStreamContinue(Request $request)
    {
        $nowTime = Carbon::now('Europe/Moscow')->startOfMinute();
        $streamId = $request->stream_id;
        $userId = $request->user_id;
        $channelName = $request->channel;
        $bitrate = $request->bitrate;
        $streamItem = TgStream::where('id', '=', $streamId)->first();
        $streamSettings = TgStreamSettings::where('stream_id', '=', $streamId)
        ->first();
        CronLogs::create([
            'uid'        => $userId,
            'cron_type' => 'tg_streams',
            'item_id' => $streamId,
            'message'   => 'Сюда дошёл1'
        ]);
        if($streamItem->live < 1){
            $streamItem->live = 1;
            $streamItem->start_count = $streamItem->start_count + 1;
            $streamItem->updated_at = $nowTime->format('Y-m-d H:i:s');
            $streamItem->save();
            $apiId = env('TELEGRAM_APP_ID');
            $apiHash = env('TELEGRAM_APP_HASH');
            $fileLoadUrl = 'files/users/' . $userName . '/tg_session';
            $logger = (new LoggerSettings)
                ->setType(Logger::FILE_LOGGER)
                ->setExtra(storage_path('app/public/'.$fileLoadUrl.'/madeline.log'))
                ->setLevel(Logger::FATAL_ERROR )
                ->setMaxSize(2048);
            $settings = 
            (new Settings())
            ->setAppInfo((new AppInfo)->setApiId($apiId)->setApiHash($apiHash))
            ->setLogger($logger);
            $sessionUrl = storage_path('app/public/' . $fileLoadUrl . '/session.madeline');
            $MadelineProto = new API($sessionUrl, $settings);
            $messages_Chats = $MadelineProto->channels->getAdminedPublicChannels(by_location: 0, check_limit: 0, );
            $channels = [];
            if(count($messages_Chats['chats']) > 0){
                foreach ($messages_Chats['chats'] as $chat) {
                    if(!$chat['left']){
                        $channels = $chat['username'];
                    }
                }
                if(in_array($channelName, $channels)){
                    CronLogs::create([
                        'uid'        => $userId,
                        'cron_type' => 'tg_streams',
                        'item_id' => $streamId,
                        'message'   => 'Сюда дошёл2'
                    ]);
                    $phone_GroupCallStreamRtmpUrl = $MadelineProto->phone->getGroupCallStreamRtmpUrl(peer: $channelName, revoke: 0);
                    $streamUrl = $phone_GroupCallStreamRtmpUrl['url'];
                    $streamKey = $phone_GroupCallStreamRtmpUrl['key'];
                    $createGroupCall = $MadelineProto->phone->createGroupCall(rtmp_stream: 1, peer: $channelName, title: $streamItem->title);
                    $inputGroupCall = $createGroupCall['updates'][3]['message']['action']['call'];
                    $streamItem->error_fix = serialize($inputGroupCall);
                    $streamItem->save();
                    $fileLoadUrl = unserialize($streamItem->link)[0];
                    $fileSend = Storage::path('public/' . $fileLoadUrl);
                    $commandExec = "ffmpeg -re -i " . $fileSend . " -c:v libx264 -c:a aac -ar 44100 -b:v ". $bitrate ." -f flv " . $streamUrl . $streamKey . "";
                    exec($commandExec, $commantOutput, $commantReturn);
                    $discardGroupCall = $MadelineProto->phone->discardGroupCall(call: $inputGroupCall);
                    $removeUrl = explode("/",$fileLoadUrl)[4];
                    exec('kill "$(pgrep -f '.$removeUrl.')"');
                    $streamItem->live = 0;
                    $streamItem->error_fix = null;
                    $streamItem->save();
                }else{
                    CronLogs::create([
                        'uid'        => $userId,
                        'cron_type' => 'tg_streams',
                        'item_id' => $streamId,
                        'message'   => 'Нужный канал не найден'
                    ]);
                }
            }
        }
    }
}
