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

use App\Models\Stories;
use App\Models\StoriesStory;
use App\Models\StoriesSettings;

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
        $allStories = Stories::where('active', 1)->get();
        foreach ($allStories as $stories) {
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stories->updated_at, 'Europe/Moscow')->startOfMinute();
            $updatedAtNext = $updatedAt->copy()->addDay()->startOfMinute();
            $accountId = $stories->account_id;
            $groupId = $stories->group_id;
            $vkAccount = VkAccount::where('id', '=', $accountId)->first();
            $userId = $vkAccount->uid;
            $vkToken = $vkAccount->token;
            if (strtotime($updatedAtNext) === strtotime(Carbon::now('Europe/Moscow')->startOfMinute())) {
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'stories',
                    'item_id' => $stories->id,
                    'message'   => 'Время совпало!'
                ]);
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
                            if(count(unserialize($stories->filetype)) > 1 && $subscribePackage['type3'] !== 1){
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'stories',
                                    'item_id' => $stories->id,
                                    'message'   => 'Пользователь не имеет Stories type3'
                                ]);
                                die();
                            }
                            $storiesDid = [];
                            foreach (unserialize($stories->filetype) as $storiesIndex => $storiesType) {
                                if ($storiesType == 'video') {
                                    $method = '/stories.getVideoUploadServer';
                                } else{
                                    $method = '/stories.getPhotoUploadServer';
                                }
                                $fileLoadUrl = unserialize($stories->link)[$storiesIndex];
                                // Создаём ссылку для загнрузки файла
                                $getUpload = new Client([
                                    'base_uri' => 'https://api.vk.com',
                                ]);
                                $uploadParams = [
                                    'v' => '5.131',
                                    'access_token' => $vkToken,
                                    'add_to_news' => 1
                                ];
                                if ($groupId > 0) {
                                    $settings = StoriesSettings::where('stories_id', '=', $stories->id)->first();
                                    $uploadParams['group_id'] = $groupId;
                                    $uploadParams['link_text'] = $settings->link_text;
                                    $uploadParams['link_url'] = $settings->link_url;
                                }
                                $uploadGet = $getUpload->request('GET', '/method' . $method, [
                                    'query' => $uploadParams
                                ]);
                                if ($uploadGet->getStatusCode() !== 200 || !$uploadGet->getBody()) {
                                    CronLogs::create([
                                        'uid'        => $userId,
                                        'cron_type' => 'stories',
                                        'item_id' => $stories->id,
                                        'message'   => 'Метод '.$method.' ничего не вернул для файла '.$storiesIndex
                                    ]);
                                    die();
                                }
                                $uploadContent = $uploadGet->getBody()->getContents();
                                $uploadJSON = json_decode($uploadContent);
                                if (isset($uploadJSON->error)) {
                                    CronLogs::create([
                                        'uid'        => $userId,
                                        'cron_type' => 'stories',
                                        'item_id' => $stories->id,
                                        'message'   => 'Метод '.$method.' вернул ошибку для файла '.$storiesIndex
                                    ]);
                                    die();
                                }
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'stories',
                                    'item_id' => $stories->id,
                                    'message'   => 'ССЫЛКА ПОЛУЧЕНА!'
                                ]);
                                $fileSend = Storage::path('public/' . $fileLoadUrl);
                                // Эта хуйня работает только с курлом ХЗ почему
                                $sendStoriesPost = curl_init();
                                if ($storiesType == 'photo') {
                                    $postFields = [
                                        'file' => new CURLFile($fileSend)
                                    ];
                                } else {
                                    $postFields = [
                                        'video_file' => new CURLFile($fileSend)
                                    ];
                                }
                                curl_setopt_array($sendStoriesPost, [
                                    CURLOPT_URL => $uploadJSON->response->upload_url,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_POST => true,
                                    CURLOPT_POSTFIELDS => $postFields,
                                ]);
                                $sendStoriesContent = curl_exec($sendStoriesPost);
                                $sendStoriesJSON = json_decode($sendStoriesContent);
                                curl_close($sendStoriesPost);
                                $sendStoriesCode = curl_getinfo($sendStoriesPost, CURLINFO_HTTP_CODE);
                                if ($sendStoriesCode != 200 || isset($sendStoriesJSON->error)) {
                                    CronLogs::create([
                                        'uid'        => $userId,
                                        'cron_type' => 'stories',
                                        'item_id' => $stories->id,
                                        'message'   => 'При загрузке файла на сервер ВК возникла ошибка'
                                    ]);
                                    die();
                                }
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'stories',
                                    'item_id' => $stories->id,
                                    'message'   => 'ФАЙЛ ЗАГРУЖЕН!'
                                ]);
                                $storiesSave = new Client([
                                    'base_uri' => 'https://api.vk.com',
                                ]);
                                $storiesParams = [
                                    'v' => '5.131',
                                    'access_token' => $vkToken,
                                    'upload_results' => $sendStoriesJSON->response->upload_result,
                                ];
                                $storiesGet = $storiesSave->request('GET', '/method/stories.save', [
                                    'query' => $storiesParams
                                ]);
                                if ($storiesGet->getStatusCode() !== 200 || !$storiesGet->getBody()) {
                                    CronLogs::create([
                                        'uid'        => $userId,
                                        'cron_type' => 'stories',
                                        'item_id' => $stories->id,
                                        'message'   => 'Метод stories.save ничего не вернул для файла '.$storiesIndex
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
                                        'message'   => 'Метод stories.save вернул ошибку для файла '.$storiesIndex
                                    ]);
                                    die();
                                }
                                $storiesDid[] = $storiesJSON->response->items[0]->id;
                                CronLogs::create([
                                    'uid'        => $userId,
                                    'cron_type' => 'stories',
                                    'item_id' => $stories->id,
                                    'message'   => 'Метод stories.save завершён для файла '.$storiesIndex
                                ]);
                            }
                            CronLogs::create([
                                'uid'        => $userId,
                                'cron_type' => 'stories',
                                'item_id' => $stories->id,
                                'message'   => 'Истории должны быть запущены!'
                            ]);
                            $stories->updated_at = Carbon::now('Europe/Moscow')->startOfMinute()->format('Y-m-d H:i:s');
                            $stories->save();
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
                            die();
                        }
                    }else{
                        CronLogs::create([
                            'uid'        => $userId,
                            'cron_type' => 'stories',
                            'item_id' => $stories->id,
                            'message'   => 'Пользователь не имеет активной Подписки'
                        ]);
                        $stories->active = 0;
                        $stories->save();
                    }
                }else{
                    CronLogs::create([
                        'uid'        => $userId,
                        'cron_type' => 'stories',
                        'item_id' => $stories->id,
                        'message'   => 'Пользователь не имеет Подписки в базе данных'
                    ]);
                }
            }elseif (strtotime($updatedAtNext->subMinutes(5)) === strtotime(Carbon::now('Europe/Moscow')->startOfMinute())) {
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'stories',
                    'item_id' => $stories->id,
                    'message'   => 'Время меньше на 5 минут!'
                ]);
                $ownerId = preg_replace("/[^0-9]/", "", $vkAccount->link);
                $lastStories = StoriesStory::where('stories_id', '=', $stories->id)->latest('id')->first();
                $viewes = $lastStories->viewes;
                $open_link = $lastStories->open_link;
                $shares = $lastStories->shares;
                foreach (unserialize($lastStories->stories_did) as $storiesIndex => $storiesItem) {
                    $storiesStats = new Client([
                        'base_uri' => 'https://api.vk.com',
                    ]);
                    $storiesParams = [
                        'v' => '5.131',
                        'access_token' => $vkToken,
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
                        die();
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
                    CronLogs::create([
                        'uid'        => $userId,
                        'cron_type' => 'stories',
                        'item_id' => $stories->id,
                        'message'   => 'Метод stories.getStats завершён для файла '.$storiesIndex
                    ]);
                }
                CronLogs::create([
                    'uid'        => $userId,
                    'cron_type' => 'stories',
                    'item_id' => $stories->id,
                    'message'   => 'Статистика историй должна быть собрана!'
                ]);
                $lastStories->viewes = $viewes;
                $lastStories->shares = $shares;
                $lastStories->open_link = $open_link;
                $lastStories->save();
            }
        }
    }
}
