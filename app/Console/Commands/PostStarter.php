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
            $postSettings = PostSettings::where('post_id', $post->id)->first();
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $post->updated_at, 'Europe/Moscow')->startOfMinute();
            $nowTime = Carbon::now('Europe/Moscow')->startOfMinute();
            $accountId = $post->account_id;
            $groupId = $post->group_id;
            $vkAccount = VkAccount::where('id', '=', $accountId)->first();
            $ownerId = preg_replace("/[^0-9]/", "", $vkAccount->link);
            $userId = $vkAccount->uid;
            $vkToken = $vkAccount->token;
            if($postSettings->type === 'date'){
                $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $postSettings->date, 'Europe/Moscow')->startOfMinute();
            }elseif ($postSettings->type === 'interval') {
                $updatedAt->addMinutes($postSettings->interval);
            }else{
                $updatedAt = $nowTime->setTime($postSettings->daily, 0, 0);
            }
            if (strtotime($updatedAt) === strtotime($nowTime)) {
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
                            $wallPostSave = new Client([
                                'base_uri' => 'https://api.vk.com',
                            ]);
                            $wallPostParams = [
                                'v' => '5.131',
                                'access_token' => $vkToken,
                                'friends_only' => $postSettings->friends_only,
                                'close_comments' => $postSettings->close_comments,
                                'message' => $post->message,
                                'attachments' => implode(',',unserialize($post->attachments)),
                            ];
                            if($vkGroup){
                                $wallPostParams['owner_id'] = '-'.$groupId;
                                $wallPostParams['from_group'] = 1;
                            }else{
                                $wallPostParams['owner_id'] = $ownerId;
                            }
                            $wallPostGet = $wallPostSave->request('GET', '/method/wall.post', [
                                'query' => $wallPostParams
                            ]);
                            if ($wallPostGet->getStatusCode() !== 200 || !$wallPostGet->getBody()) {
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'posts',
                                    'item_id' => $post->id,
                                    'message'   => 'Метод wall.post ничего не вернул'
                                ]);
                                die();
                            }
                            $wallPostContent = $wallPostGet->getBody()->getContents();
                            $wallPostJSON = json_decode($wallPostContent);
                            if (isset($wallPostJSON->error)) {
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'posts',
                                    'item_id' => $post->id,
                                    'message'   => 'Метод wall.post вернул ошибку'
                                ]);
                                die();
                            }
                            
                            $latestPost = PostStory::where('post_id', '=', $post->id)->latest()->first();
                            if ($latestPost->post_did === 0) {
                                $latestPost->post_did = $wallPostJSON->response->post_id;
                                $latestPost->save();
                            }else{
                                if($postSettings->remove_old === 1){
                                    $wallPostRemove = new Client([
                                        'base_uri' => 'https://api.vk.com',
                                    ]);
                                    $wallRemoveParams = [
                                        'v' => '5.131',
                                        'access_token' => $vkToken,
                                        'post_id' => $latestPost->post_did
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
                                            'cron_type' => 'posts',
                                            'item_id' => $post->id,
                                            'message'   => 'Метод wall.delete ничего не вернул'
                                        ]);
                                        die();
                                    }
                                    $wallRemoveContent = $wallRemoveGet->getBody()->getContents();
                                    $wallRemoveJSON = json_decode($wallRemoveContent);
                                    if (isset($wallRemoveJSON->error)) {
                                        CronLogs::create([
                                            'uid'        => $userId,
                                            'cron_type' => 'posts',
                                            'item_id' => $post->id,
                                            'message'   => 'Метод wall.delete вернул ошибку'
                                        ]);
                                        die();
                                    }
                                }
                                PostStory::create([
                                    'post_id' => $post->id,
                                    'post_did' => $wallPostJSON->response->post_id
                                ]);
                            }
                            if($postSettings->pin_new === 1){
                                $postPinClient = new Client([
                                    'base_uri' => 'https://api.vk.com',
                                ]);
                                $postPinParams = [
                                    'v' => '5.131',
                                    'access_token' => $vkToken,
                                    'post_id' => $wallPostJSON->response->post_id
                                ];
                                if ($vkGroup) {
                                    $postPinParams['owner_id'] = '-'.$groupId;
                                }else{
                                    $postPinParams['owner_id'] = $ownerId;
                                }
                                $postPinGet = $postPinClient->request('GET', '/method/wall.pin', [
                                    'query' => $postPinParams
                                ]);
                                if ($postPinGet->getStatusCode() !== 200 || !$postPinGet->getBody()) {
                                    CronLogs::create([
                                        'uid'        => $userId,
                                        'cron_type' => 'posts',
                                        'item_id' => $post->id,
                                        'message'   => 'Метод wall.pin ничего не вернул'
                                    ]);
                                    die();
                                }
                                $postPinContent = $postPinGet->getBody()->getContents();
                                $postPinJSON = json_decode($postPinContent);
                                if (isset($postPinJSON->error)) {
                                    CronLogs::create([
                                        'uid'        => $userId,
                                        'cron_type' => 'posts',
                                        'item_id' => $post->id,
                                        'message'   => 'Метод wall.pin вернул ошибку'
                                    ]);
                                    die();
                                }
                            }
                            $post->updated_at = $nowTime->format('Y-m-d H:i:s');
                            $post->save();
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
            }elseif (strtotime($updatedAt->subMinutes(5)) === strtotime($nowTime)) {
                $lastPost = PostStory::where('post_id', '=', $post->id)->latest('id')->first();
                if($lastPost->posd_did !== 0){
                    $comments = $lastPost->comments;
                    $reposts = $lastPost->reposts;
                    $likes = $lastPost->likes;
                    $viewes = $lastPost->viewes;
                    $postStats = new Client([
                        'base_uri' => 'https://api.vk.com',
                    ]);
                    $postParams = [
                        'v' => '5.131',
                        'access_token' => $vkToken,
                        'post_id' => $lastPost->post_did,
                    ];
                    $postGet = $postStats->request('GET', '/method/wall.getById', [
                        'query' => $postParams
                    ]);
                    if ($postGet->getStatusCode() !== 200 || !$postGet->getBody()) {
                        CronLogs::create([
                            'uid'        => $userId,
                            'cron_type' => 'posts',
                            'item_id' => $post->id,
                            'message'   => 'Метод wall.getById ничего не вернул!'
                        ]);
                        die();
                    }
                    $postContent = $postGet->getBody()->getContents();
                    $postJSON = json_decode($postContent);
                    if (isset($postJSON->error)) {
                        CronLogs::create([
                            'uid'        => $userId,
                            'cron_type' => 'posts',
                            'item_id' => $post->id,
                            'message'   => 'Метод wall.getById вернул ошибку!'
                        ]);
                        die();
                    }
                    if($postJSON->response[0]->comments){
                        $comments += $postJSON->response[0]->comments->count;
                    }
                    if($postJSON->response[0]->likes){
                        $likes += $postJSON->response[0]->likes->count;
                    }
                    if($postJSON->response[0]->reposts){
                        $reposts += $postJSON->response[0]->reposts->count;
                    }
                    if($postJSON->response[0]->views){
                        $viewes += $postJSON->response[0]->views->count;
                    }
                    $lastPost->viewes = $viewes;
                    $lastPost->comments = $comments;
                    $lastPost->reposts = $reposts;
                    $lastPost->likes = $likes;
                    $lastPost->save();
                }
            }
        }
    }
}
