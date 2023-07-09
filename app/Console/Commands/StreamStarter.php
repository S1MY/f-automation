<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

use Carbon\Carbon;

use App\Models\User;
use App\Models\Subscribe;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stream;
use App\Models\StreamStory;
use App\Models\StreamSettings;
use App\Models\StreamComments;

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
        $allStreams = Stream::where('active', 1)->where('live', 0)->get();
        foreach ($allStreams as $stream) {
            $currentDomain = 'https://f-automation.v-devo.ru';
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
                            $client = new Client();
                            $streamDescription = $stream->description .'<br>Трансляция воспроизводится F-AUTOMATION: '.$currentDomain.'?partner='.$userDB->login;
                            $streamContinue = $currentDomain.'/stream-continue';
                            $postContent = [
                                'stream_id' => $stream->id,
                                'user_id' => $userId,
                                'token' => $vkToken,
                                'description' => $streamDescription,
                            ];
                            if($subscribePackage['type3'] === 1){
                                $postContent['bitrate'] = '5120k';
                            }else{
                                $postContent['bitrate'] = '2048k';
                            }
                            $request = new Request('POST', $streamContinue);
                            $options = [
                                'query' => $postContent
                            ];
                            
$response = $client->send($request, $options);
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
            }elseif (strtotime($updatedAt->subMinutes(5)) === strtotime($nowTime)) {
                if($postSettings->type !== 'date'){
                    $lastStream = StreamStory::where('stream_id', '=', $stream->id)->latest('id')->first();
                    if($lastStream){
                        // Собираем статистику
                        $streamStatsClient = new Client([
                            'base_uri' => 'https://api.vk.com',
                        ]);
                        $streamStatsParams = [
                            'v' => '5.131',
                            'access_token' => $vkToken,
                        ];
                        if ($vkGroup) {
                            $streamStatsParams['videos'] = '-'.$groupId.'_'.$lastStream->video_did;
                        }else{
                            $streamStatsParams['videos'] = $ownerId.'_'.$lastStream->video_did;
                        }
                        $streamStatsGet = $streamStatsClient->request('GET', '/method/video.get', [
                            'query' => $streamStatsParams
                        ]);
                        if ($streamStatsGet->getStatusCode() !== 200 || !$streamStatsGet->getBody()) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'streams',
                                'item_id' => $stream->id,
                                'message'   => 'Метод video.get ничего не вернул!'
                            ]);
                        }
                        $streamStatsContent = $streamStatsGet->getBody()->getContents();
                        $streamStatsJSON = json_decode($streamStatsContent);
                        if (isset($streamStatsJSON->error)) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'streams',
                                'item_id' => $stream->id,
                                'message'   => 'Метод video.get вернул ошибку!'
                            ]);
                        }
                        foreach ($streamStatsJSON->response->items as $statItem) {
                            if($statItem->id == $lastStream->video_did){
                                $lastStream->comments = $statItem->comments;
                                $lastStream->likes = $statItem->likes->count;
                                $lastStream->views = $statItem->views;
                                $lastStream->reposts = $statItem->reposts->count;
                                $lastStream->save();
                            }
                        }
                        // Собираем комментарии
                        $streamCommentsClient = new Client([
                            'base_uri' => 'https://api.vk.com',
                        ]);
                        $streamCommentsParams = [
                            'v' => '5.131',
                            'access_token' => $vkToken,
                            'video_id' => $lastStream->video_did
                        ];
                        if ($vkGroup) {
                            $streamCommentsParams['owner_id'] = '-'.$groupId;
                        }else{
                            $streamCommentsParams['owner_id'] = $ownerId;
                        }
                        $streamCommentsGet = $streamCommentsClient->request('GET', '/method/video.getComments', [
                            'query' => $streamCommentsParams
                        ]);
                        if ($streamCommentsGet->getStatusCode() !== 200 || !$streamCommentsGet->getBody()) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'streams',
                                'item_id' => $stream->id,
                                'message'   => 'Метод video.getComments ничего не вернул'
                            ]);
                        }
                        $streamCommentsContent = $streamCommentsGet->getBody()->getContents();
                        $streamCommentsJSON = json_decode($streamCommentsContent);
                        if (isset($streamCommentsJSON->error)) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'streams',
                                'item_id' => $stream->id,
                                'message'   => 'Метод video.getComments вернул ошибку'
                            ]);
                        }
                        foreach ($streamCommentsJSON->response->items as $commentItem) {
                            $carbonDate = Carbon::createFromTimestamp($commentItem->date);
                            $fromUser = $commentItem->from_id;
                            // Получаем о юзере
                            $authClient = new Client([
                                'base_uri' => 'https://api.vk.com',
                            ]);
                            $usersParams = [
                                'v' => '5.131',
                                'access_token' => $vkToken,
                                'user_ids' => $fromUser,
                                'fields' => 'first_name, last_name, photo_200_orig'
                            ];
                            $usersGet = $authClient->request('GET', '/method/users.get', [
                                'query' => $usersParams
                            ]);
                            if ($usersGet->getStatusCode() !== 200 || !$usersGet->getBody()) {
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'users',
                                    'item_id' => $stream->id,
                                    'message'   => 'Метод users.get ничего не вернул'
                                ]);
                            }
                            $usersContent = $usersGet->getBody()->getContents();
                            $usersJSON = json_decode($usersContent);
                            if (isset($usersJSON->error)) {
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'users',
                                    'item_id' => $stream->id,
                                    'message'   => 'Метод users.get вернул ошибку'
                                ]);
                            }
                            $accountName = $usersJSON->response[0]->first_name . ' ' . $usersJSON->response[0]->last_name;
                            $accountLink = 'https://vk.com/id' . $usersJSON->response[0]->id;
                            $accountAvatar = $usersJSON->response[0]->photo_200_orig;
                            // Сохраняем
                            StreamComments::create([
                                'stream_id' => $stream->id,
                                'stream_story_id' => $lastStream->id,
                                'comment' => $commentItem->text,
                                'time' => $carbonDate,
                                'name' => $accountName,
                                'avatar' => $accountAvatar,
                                'link' => $accountLink,
                            ]);
                        }
                        // Удаление стрима
                        $videoRemoveClient = new Client([
                            'base_uri' => 'https://api.vk.com',
                        ]);
                        $videoRemoveParams = [
                            'v' => '5.131',
                            'access_token' => $vkToken,
                            'video_id' => $lastStream->video_did,
                        ];
                        if ($vkGroup) {
                            $videoRemoveParams['owner_id'] = '-'.$groupId;
                            $videoRemoveParams['target_id'] = '-'.$groupId;
                        }else{
                            $videoRemoveParams['owner_id'] = $ownerId;
                            $videoRemoveParams['target_id'] = $ownerId;
                        }
                        $videoRemoveGet = $videoRemoveClient->request('GET', '/method/video.delete', [
                            'query' => $videoRemoveParams
                        ]);
                
                        if ($videoRemoveGet->getStatusCode() !== 200 || !$videoRemoveGet->getBody()) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'users',
                                'item_id' => $stream->id,
                                'message'   => 'Метод video.delete вернул ошибку'
                            ]);
                        }
                        $videoRemoveContent = $videoRemoveGet->getBody()->getContents();
                        $videoRemoveJSON = json_decode($videoRemoveContent);
                        if (isset($videoRemoveJSON->error)) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'users',
                                'item_id' => $stream->id,
                                'message'   => 'Метод video.delete вернул ошибку'
                            ]);
                        }
                        // Удаление поста
                        $wallPostRemove = new Client([
                            'base_uri' => 'https://api.vk.com',
                        ]);
                        $wallRemoveParams = [
                            'v' => '5.131',
                            'access_token' => $vkToken,
                            'post_id' => $lastStream->post_did
                        ];
                        if($vkGroup){
                            $wallRemoveParams['owner_id'] = '-'.$groupId;
                        }else{
                            $wallRemoveParams['owner_id'] = $ownerId;
                        }
                        $wallRemoveGet = $wallPostRemove->request('GET', '/method/wall.delete', [
                            'query' => $wallRemoveParams
                        ]);
                        if ($wallRemoveGet->getStatusCode() !== 200 || !$wallRemoveGet->getBody()) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'streams',
                                'item_id' => $stream->id,
                                'message'   => 'Метод wall.delete ничего не вернул'
                            ]);
                        }
                        $wallRemoveContent = $wallRemoveGet->getBody()->getContents();
                        $wallRemoveJSON = json_decode($wallRemoveContent);
                        if (isset($wallRemoveJSON->error)) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'streams',
                                'item_id' => $stream->id,
                                'message'   => 'Метод wall.delete вернул ошибку'
                            ]);
                        }
                    }
                }
            }
        }
    }
}
