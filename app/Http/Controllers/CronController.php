<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\Stream;
use App\Models\StreamStory;
use App\Models\StreamSettings;

use App\Models\CronLogs;

class CronController extends Controller
{
    public function streamContinue(Request $request)
    {
        $nowTime = Carbon::now('Europe/Moscow')->startOfMinute();
        $streamId = $request->stream_id;
        $userId = $request->user_id;
        $token = $request->token;
        $description = $request->description;
        $bitrate = $request->bitrate;
        $streamItem = Stream::where('id', '=', $streamId)->first();
        $streamSettings = StreamSettings::where('stream_id', '=', $streamId)
        ->first();
        $streamStartClient = new Client([
            'base_uri' => 'https://api.vk.com',
        ]);
        $streamStartParams = [
            'v' => '5.131',
            'access_token' => $token,
            'wallpost' => 1,
            'name' => $streamItem->title,
            'description' => $description
        ];
        if($streamItem->group_id > 0){
            $streamStartParams['group_id'] = $streamItem->group_id;
        }
        if($streamItem->live < 1){
            $streamStartGet = $streamStartClient->request('GET', '/method/video.startStreaming', [
                'query' => $streamStartParams
            ]);
            if ($streamStartGet->getStatusCode() !== 200 || !$streamStartGet->getBody()) {
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'streams',
                    'item_id' => $streamItem->id,
                    'message'   => 'Метод video.startStreaming ничего не вернул!'
                ]);
                die();
            }
            $streamStartContent = $streamStartGet->getBody()->getContents();
            $streamStartJSON = json_decode($streamStartContent);
            if (isset($streamStartJSON->error)) {
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'streams',
                    'item_id' => $streamItem->id,
                    'message'   => 'Метод video.startStreaming вернул ошибку!'
                ]);
                die();
            }
            $streamItem->live = 1;
            $streamItem->updated_at = $nowTime->format('Y-m-d H:i:s');
            $streamItem->save();
            $streamUrl = $streamStartJSON->response->stream->url;
            $streamKey = $streamStartJSON->response->stream->key;
            $ownerId = $streamStartJSON->response->owner_id;
            $videoId = $streamStartJSON->response->video_id;
            $postId = $streamStartJSON->response->post_id;
            $fileLoadUrl = unserialize($streamItem->link)[0];
            $fileSend = Storage::path('public/' . $fileLoadUrl);
            if ($streamSettings->type !== 'date') {
                StreamStory::create([
                    'stream_id' => $streamItem->id,
                    'video_did' => $videoId,
                    'post_did' => $postId,
                ]);
            }
            $commandExec = "ffmpeg -re -i " . $fileSend . " -b:v ". $bitrate ." -f flv " . $streamUrl . $streamKey . "";
            $streamStopClient = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
            $streamStopParams = [
                'v' => '5.131',
                'access_token' => $token,
                'video_id' => $videoId
            ];
            if($streamItem->group_id > 0){
                $streamStopParams['group_id'] = $streamItem->group_id;
                $streamStopParams['owner_id'] = ltrim($ownerId, '-');
            }else{
                $streamStopParams['owner_id'] = $ownerId;
            }
            exec($commandExec, $commantOutput, $commantReturn);
            $streamStopGet = $streamStopClient->request('GET', '/method/video.stopStreaming', [
                'query' => $streamStopParams
            ]);
            if ($streamStopGet->getStatusCode() !== 200 || !$streamStopGet->getBody()) {
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'streams',
                    'item_id' => $streamItem->id,
                    'message'   => 'Метод video.stopStreaming ничего не вернул!'
                ]);
                die();
            }
            $streamStopContent = $streamStopGet->getBody()->getContents();
            $streamStopJSON = json_decode($streamStopContent);
            if (isset($streamStopJSON->error)) {
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'streams',
                    'item_id' => $streamItem->id,
                    'message'   => 'Метод video.stopStreaming вернул ошибку!'
                ]);
                die();
            }
            $removeUrl = explode("/",$fileLoadUrl)[4];
            exec('kill "$(pgrep -f '.$removeUrl.')"');
            $streamItem->live = 0;
            $streamItem->save();
        }
    }
}
