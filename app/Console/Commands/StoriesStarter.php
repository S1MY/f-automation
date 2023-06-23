<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Models\Subscribe;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;
use App\Models\StoriesStory;

use App\Models\CronLogs;

class StoriesStarter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stories:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для запуска крона Создания Историй ВК';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $allStories = Stories::all();
        foreach ($allStories as $stories) {
            if($stories->active === 1){
                $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stories->updated_at, 'Europe/Moscow')->startOfMinute();
                $updatedAtNext = $updatedAt->copy()->addDay()->startOfMinute();
                $accountId = $stories->account_id;
                $groupId = $stories->group_id;
                $vkAccount = VkAccount::where('id', '=', $accountId)->first();
                $userId = $vkAccount->uid;
                $vkToken = $vkAccount->token;
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'stories',
                    'item_id' => $stories->id,
                    'message'   => $updatedAtNext.' | '.Carbon::now('Europe/Moscow')->startOfMinute()
                ]);
                if ($updatedAtNext === Carbon::now('Europe/Moscow')->startOfMinute()) {
                    $vkGroup = ($groupId > 0) ? VkGroup::where('group_id', '=', $groupId)->first() : null;
                    $userSubscribe = Subscribe::where('uid', '=', $userId)->first();
                    if($userSubscribe){
                        if($userSubscribe->sub_status === 1){
                            $subscribePackage = unserialize($userSubscribe->sub_config)['vk']['stories'];
                            if($subscribePackage['type1'] === 1){
                                if($vkGroup && $subscribePackage['type2'] !== 1){
                                    CronLogs::create([
                                        'uid'        => $userId,
                                        'cron_type' => 'stories',
                                        'item_id' => $stories->id,
                                        'message'   => 'Пользователь не имеет Stories type2'
                                    ]);
                                    die();
                                }
                                if(count(unserialize($stories->upload_result)) > 1 && $subscribePackage['type3'] !== 1){
                                    CronLogs::create([
                                        'uid'        => $userId,
                                        'cron_type' => 'stories',
                                        'item_id' => $stories->id,
                                        'message'   => 'Пользователь не имеет Stories type3'
                                    ]);
                                    die();
                                }
                                $storiesDid = [];
                                foreach (unserialize($stories->upload_result) as $storiesIndex => $storiesUpload) {
                                    $storiesSave = new Client([
                                        'base_uri' => 'https://api.vk.com',
                                    ]);
                                    $storiesParams = [
                                        'v' => '5.131',
                                        'access_token' => $vkToken,
                                        'upload_results' => $storiesUpload,
                                    ];
                                    $storiesGet = $storiesSave->request('GET', '/method/stories.save', [
                                        'query' => $storiesParams
                                    ]);
                                    if ($storiesGet->getStatusCode() !== 200 || !$storiesGet->getBody()) {
                                        CronLogs::create([
                                            'uid'        => $userId,
                                            'cron_type' => 'stories',
                                            'item_id' => $stories->id,
                                            'message'   => 'Метод stories.save ничего не вернул!'
                                        ]);
                                        die();
                                    }
                                    $storiesContent = $storiesGet->getBody()->getContents();
                                    $storiesJSON = json_decode($storiesContent);
                                    if (isset($storiesJSON->error)) {
                                        CronLogs::create([
                                            'uid'        => $userId,
                                            'cron_type' => 'stories',
                                            'item_id' => $stories->id,
                                            'message'   => 'Метод stories.save вернул ошибку!'
                                        ]);
                                        die();
                                    }
                                    $storiesDid[] = $storiesJSON->response->items[0]->id;
                                }
                                $stories->updated_at = Carbon::now('Europe/Moscow')->startOfMinute()->format('Y-m-d H:i:s');
                                $stories-save();
                                StoriesStory::create([
                                    'stories_id' => $stories->id,
                                    'stories_did' => serialize($storiesDid)
                                ]);
                            }else{
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'stories',
                                    'item_id' => $stories->id,
                                    'message'   => 'Пользователь не имеет Stories type1'
                                ]);
                            }
                        }else{
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'stories',
                                'item_id' => $stories->id,
                                'message'   => 'Пользователь не имеет активной Подписки'
                            ]);
                            $stories->active = 0;
                            $stories-save();
                        }
                    }else{
                        CronLogs::create([
                            'uid'        => $userId,
                            'cron_type' => 'stories',
                            'item_id' => $stories->id,
                            'message'   => 'Пользователь не имеет Подписки в базе данных'
                        ]);
                    }
                }elseif ($updatedAtNext->subMinutes(5) === Carbon::now('Europe/Moscow')->startOfMinute()) {
                    $ownerId = preg_replace("/[^0-9]/", "", $vkAccount->link);
                    $lastStories = StoriesStory::where('stories_id', '=', $stories->id)->latest('id')->first();
                    $viewes = $lastStories->viewes;
                    $open_link = $lastStories->open_link;
                    $shares = $lastStories->shares;
                    foreach (unserialize($lastStories->stories_did) as $storiesItem) {
                        $storiesStats = new Client([
                            'base_uri' => 'https://api.vk.com',
                        ]);
                        $storiesParams = [
                            'v' => '5.131',
                            'owner_id' => $ownerId,
                            'story_id' => $storiesItem,
                        ];
                        $storiesGet = $storiesStats->request('GET', '/method/stories.getStats', [
                            'query' => $storiesParams
                        ]);
                        if ($storiesGet->getStatusCode() !== 200 || !$storiesGet->getBody()) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'stories',
                                'item_id' => $stories->id,
                                'message'   => 'Метод stories.getStats ничего не вернул!'
                            ]);
                            die();
                        }
                        $storiesContent = $storiesGet->getBody()->getContents();
                        $storiesJSON = json_decode($storiesContent);
                        if (isset($storiesJSON->error)) {
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'stories',
                                'item_id' => $stories->id,
                                'message'   => 'Метод stories.getStats вернул ошибку!'
                            ]);
                        }
                        if($storiesJSON->response->views->state === "on"){
                            $viewes += $storiesJSON->response->views->count;
                        }
                        if($storiesJSON->response->shares->state === "on"){
                            $shares += $storiesJSON->response->shares->count;
                        }
                        if($storiesJSON->response->open_link->state === "on"){
                            $open_link += $storiesJSON->response->open_link->count;
                        }
                    }
                    $lastStories->viewes = $viewes;
                    $lastStories->shares = $shares;
                    $lastStories->open_link = $open_link;
                    $lastStories->save();
                }
            }
        }
    }
}
