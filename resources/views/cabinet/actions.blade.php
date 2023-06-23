@php
    
use GuzzleHttp\Client;
use Carbon\Carbon;

use App\Models\Subscribe;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;
use App\Models\StoriesStory;
use App\Models\StoriesSettings;

use App\Models\CronLogs;
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
                    'message'   => 'Воркерс'
                ]);
                // if (strtotime($updatedAtNext) === strtotime(Carbon::now('Europe/Moscow')->startOfMinute())) {
                    // $vkGroup = ($groupId > 0) ? VkGroup::where('group_id', '=', $groupId)->first() : null;
                    // $userSubscribe = Subscribe::where('uid', '=', $userId)->first();
                    // if($userSubscribe){
                    //     if($userSubscribe->sub_status === 1){
                    //         $subscribePackage = unserialize($userSubscribe->sub_config)['vk']['stories'];
                    //         if($subscribePackage['type1'] === 1){
                    //             if($vkGroup && $subscribePackage['type2'] !== 1){
                    //                 CronLogs::create([
                    //                     'uid'        => $userId,
                    //                     'cron_type' => 'stories',
                    //                     'item_id' => $stories->id,
                    //                     'message'   => 'Пользователь не имеет Stories type2'
                    //                 ]);
                    //                 die();
                    //             }
                    //             if(count(unserialize($stories->filetype)) > 1 && $subscribePackage['type3'] !== 1){
                    //                 CronLogs::create([
                    //                     'uid'        => $userId,
                    //                     'cron_type' => 'stories',
                    //                     'item_id' => $stories->id,
                    //                     'message'   => 'Пользователь не имеет Stories type3'
                    //                 ]);
                    //                 die();
                    //             }
                    //             $storiesDid = [];
                    //             foreach (unserialize($stories->filetype) as $storiesIndex => $storiesType) {
                    //                 if ($storiesType == 'video') {
                    //                     $method = '/stories.getVideoUploadServer';
                    //                 } else{
                    //                     $method = '/stories.getPhotoUploadServer';
                    //                 }
                    //                 $fileLoadUrl = unserialize($stories->link)[$storiesIndex];
                    //                 // Создаём ссылку для загнрузки файла
                    //                 $getUpload = new Client([
                    //                     'base_uri' => 'https://api.vk.com',
                    //                 ]);
                    //                 $uploadParams = [
                    //                     'v' => '5.131',
                    //                     'access_token' => $vkToken,
                    //                     'add_to_news' => 1
                    //                 ];
                    //                 if ($groupId > 0) {
                    //                     $settings = StoriesSettings::where('stories_id', '=', $stories->id)->first();
                    //                     $uploadParams['group_id'] = $groupId;
                    //                     $uploadParams['link_text'] = $settings->link_text;
                    //                     $uploadParams['link_url'] = $settings->link_url;
                    //                 }
                    //                 $uploadGet = $getUpload->request('GET', '/method' . $method, [
                    //                     'query' => $uploadParams
                    //                 ]);
                    //                 if ($uploadGet->getStatusCode() !== 200 || !$uploadGet->getBody()) {
                    //                     CronLogs::create([
                    //                         'uid'        => $userId,
                    //                         'cron_type' => 'stories',
                    //                         'item_id' => $stories->id,
                    //                         'message'   => 'Метод '.$method.' ничего не вернул для файла '.$storiesIndex
                    //                     ]);
                    //                     die();
                    //                 }
                    //                 $uploadContent = $uploadGet->getBody()->getContents();
                    //                 $uploadJSON = json_decode($uploadContent);
                    //                 if (isset($uploadJSON->error)) {
                    //                     CronLogs::create([
                    //                         'uid'        => $userId,
                    //                         'cron_type' => 'stories',
                    //                         'item_id' => $stories->id,
                    //                         'message'   => 'Метод '.$method.' вернул ошибку для файла '.$storiesIndex
                    //                     ]);
                    //                     die();
                    //                 }
                    //                 $fileSend = storage_path('app/public/' . $fileLoadUrl);
                    //                 // Эта хуйня работает только с курлом ХЗ почему
                    //                 $sendStoriesPost = curl_init();
                    //                 if ($storiesType == 'photo') {
                    //                     $postFields = [
                    //                         'file' => new CURLFile($fileSend)
                    //                     ];
                    //                 } else {
                    //                     $postFields = [
                    //                         'video_file' => new CURLFile($fileSend)
                    //                     ];
                    //                 }
                    //                 curl_setopt_array($sendStoriesPost, [
                    //                     CURLOPT_URL => $uploadJSON->response->upload_url,
                    //                     CURLOPT_RETURNTRANSFER => true,
                    //                     CURLOPT_POST => true,
                    //                     CURLOPT_POSTFIELDS => $postFields,
                    //                 ]);
                    //                 $sendStoriesContent = curl_exec($sendStoriesPost);
                    //                 $sendStoriesJSON = json_decode($sendStoriesContent);
                    //                 curl_close($sendStoriesPost);
                    //                 $sendStoriesCode = curl_getinfo($sendStoriesPost, CURLINFO_HTTP_CODE);
                    //                 if ($sendStoriesCode != 200 || isset($sendStoriesJSON->error)) {
                    //                     CronLogs::create([
                    //                         'uid'        => $userId,
                    //                         'cron_type' => 'stories',
                    //                         'item_id' => $stories->id,
                    //                         'message'   => 'При загрузке файла на сервер ВК возникла ошибка'
                    //                     ]);
                    //                     die();
                    //                 }
                    //                 $storiesSave = new Client([
                    //                     'base_uri' => 'https://api.vk.com',
                    //                 ]);
                    //                 $storiesParams = [
                    //                     'v' => '5.131',
                    //                     'access_token' => $vkToken,
                    //                     'upload_results' => $sendStoriesJSON->response->upload_result,
                    //                 ];
                    //                 $storiesGet = $storiesSave->request('GET', '/method/stories.save', [
                    //                     'query' => $storiesParams
                    //                 ]);
                    //                 if ($storiesGet->getStatusCode() !== 200 || !$storiesGet->getBody()) {
                    //                     CronLogs::create([
                    //                         'uid'        => $userId,
                    //                         'cron_type' => 'stories',
                    //                         'item_id' => $stories->id,
                    //                         'message'   => 'Метод stories.save ничего не вернул для файла '.$storiesIndex
                    //                     ]);
                    //                     die();
                    //                 }
                    //                 $storiesContent = $storiesGet->getBody()->getContents();
                    //                 $storiesJSON = json_decode($storiesContent);
                    //                 if (isset($storiesJSON->error)) {
                    //                     CronLogs::create([
                    //                         'uid'        => $userId,
                    //                         'cron_type' => 'stories',
                    //                         'item_id' => $stories->id,
                    //                         'message'   => 'Метод stories.save вернул ошибку для файла '.$storiesIndex
                    //                     ]);
                    //                     die();
                    //                 }
                    //                 $storiesDid[] = $storiesJSON->response->items[0]->id;
                    //                 CronLogs::create([
                    //                     'uid'        => $userId,
                    //                     'cron_type' => 'stories',
                    //                     'item_id' => $stories->id,
                    //                     'message'   => 'Метод stories.save завершён для файла '.$storiesIndex
                    //                 ]);
                    //             }
                    //             CronLogs::create([
                    //                 'uid'        => $userId,
                    //                 'cron_type' => 'stories',
                    //                 'item_id' => $stories->id,
                    //                 'message'   => 'Истории должны быть запущены!'
                    //             ]);
                    //             $userSubscribe = Subscribe::where('uid', '=', $userId)->first();
                    //             $stories->updated_at = Carbon::now('Europe/Moscow')->startOfMinute()->format('Y-m-d H:i:s');
                    //             $stories->save();
                    //             StoriesStory::create([
                    //                 'stories_id' => $stories->id,
                    //                 'stories_did' => serialize($storiesDid)
                    //             ]);
                    //         }else{
                    //             CronLogs::create([
                    //                 'uid'        => $userId,
                    //                 'cron_type' => 'stories',
                    //                 'item_id' => $stories->id,
                    //                 'message'   => 'Пользователь не имеет Stories type1'
                    //             ]);
                    //         }
                    //     }else{
                    //         CronLogs::create([
                    //             'uid'        => $userId,
                    //             'cron_type' => 'stories',
                    //             'item_id' => $stories->id,
                    //             'message'   => 'Пользователь не имеет активной Подписки'
                    //         ]);
                    //         $stories->active = 0;
                    //         $stories->save();
                    //     }
                    // }else{
                    //     CronLogs::create([
                    //         'uid'        => $userId,
                    //         'cron_type' => 'stories',
                    //         'item_id' => $stories->id,
                    //         'message'   => 'Пользователь не имеет Подписки в базе данных'
                    //     ]);
                    // }
                // }elseif (strtotime($updatedAtNext->subMinutes(5)) === strtotime(Carbon::now('Europe/Moscow')->startOfMinute())) {
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
                        }
                        print_r($storiesJSON);
                        // if($storiesJSON->response->views->state === "on"){
                        //     $viewes += $storiesJSON->response->views->count;
                        // }
                        // if($storiesJSON->response->shares->state === "on"){
                        //     $shares += $storiesJSON->response->shares->count;
                        // }
                        // if($storiesJSON->response->open_link->state === "on"){
                        //     $open_link += $storiesJSON->response->open_link->count;
                        // }
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
                // }
            }
        }
@endphp
@extends('cabinet.layouts.master')

@section('title', 'Акции')

@section('cabinet-content')

    <div class="pageFlex">
        <div class="sidebar">
            @include('cabinet.layouts.sidebar')
        </div>
        <div class="pageWrapper">
            <div class="pageItems">
                <div class="pageItem">
                    <h1 class="pageTitle">F-Акции</h1>
                    <p class="pageText">Участвуйте в акциях и получайте скидки. Ваше участие в акциях выгодно всем.</p>
                    <p class="pageText _mt12">Мы получаем дополнительную рекламу и прирост аудитории. Вы получаете скидку на дальнейшее приобретение/продление пакета.</p>
                    <p class="pageText _mt12">Полученные промокоды можно передавать третьим лицам. Никаких ограничений нет.</p>
                    <div class="pageTabs">
                        <p class="pageTab _active" data-tab="new">Все акции</p>
                        <p class="pageTab" data-tab="you">Ваши акции</p>
                    </div>
                    <div class="tabItem _active" data-tab="new">
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-ролик</span><span class="actionDate">до 16.12.2023</span></h2>
                            <p class="actionText">С 16.11 до 16.12 включительно получи промокод номиналом 100₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку</p>
                            <p class="actionList">Стримить наш промо-ролик в течении 3 дней 6 раз</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-ролик начнёт работу автоматически и будет запускаться каждые 6 часов по 2 раза в день.</p>
                            <p class="actionText">Первый запуск в 10:00 по МСК, второй запуск в 16:00 по МСК.</p>
                            <p class="actionText">Нажимая кнопку <span class="_number">"Участвовать в акции"</span> вы принимаете все условия акции и автоматически попадаете в очередь на запуск промо-трансляции.</p>
                            <p class="actionComment">Во время активного участия в акции вы не сможете запускать другие трансляции.</p>
                            <a href="#" class="actionButton">Участвовать в акции</a>
                        </div>
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-сторис</span><span class="actionDate">до 16.12.2023</span></h2>
                            <p class="actionText">С 16.11 до 16.12 включительно получи промокод номиналом 35₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку (Истории)</p>
                            <p class="actionList">Автопост нашего промо в истории в течении 3 дней</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-сторис выложена <span class="_number">1</span> из <span class="_number">3</span> дней.</p>
                            <p class="actionCurrent">Вы уже участвуете</p>
                        </div>
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-сторис</span><span class="actionDate">до 16.10.2023</span></h2>
                            <p class="actionText">С 16.09 до 16.10 включительно получи промокод номиналом 35₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку (Истории)</p>
                            <p class="actionList">Автопост нашего промо в истории в течении 3 дней</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-сторис автоматически запустится в работув 00:00 по МСК</p>
                            <p class="actionComplete">Акция закончилась</p>
                        </div>
                    </div>
                    <div class="tabItem" data-tab="you">
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-сторис</span><span class="actionDate">до 16.12.2023</span></h2>
                            <p class="actionText">С 16.11 до 16.12 включительно получи промокод номиналом 35₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку (Истории)</p>
                            <p class="actionList">Автопост нашего промо в истории в течении 3 дней</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-сторис выложена <span class="_number">1</span> из <span class="_number">3</span> дней.</p>
                            <p class="actionCurrent">Вы уже участвуете</p>
                        </div>
                        <div class="actionItem">
                            <h2 class="pageTitle actionTitle"><span>Промо-сторис</span><span class="actionDate">до 16.11.2023</span></h2>
                            <p class="actionText">С 16.10 до 16.11 включительно получи промокод номиналом 30₽ для приобретения подписки.</p>
                            <p class="actionListName">Условия акции:</p>
                            <p class="actionList">Иметь активную подписку (Истории)</p>
                            <p class="actionList">Автопост нашего промо в истории в течении 3 дней</p>
                            <p class="actionList">Промокод действует 1 раз</p>
                            <p class="actionText">Промо-сторис выложена <span class="_number">3</span> из <span class="_number">3</span> дней.</p>
                            <p class="actionListName">Ваш промокод</p>
                            <p class="actionList">Действителен до 27.11.2023</p>
                            <div class="partnerItemChild _promocode">
                                <p class="partnerItemLink _link">PS10YES11</p>
                                <a href="#" class="partnerItemBtn">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 18C8.45 18 7.97933 17.8043 7.588 17.413C7.196 17.021 7 16.55 7 16V4C7 3.45 7.196 2.979 7.588 2.587C7.97933 2.19567 8.45 2 9 2H18C18.55 2 19.021 2.19567 19.413 2.587C19.8043 2.979 20 3.45 20 4V16C20 16.55 19.8043 17.021 19.413 17.413C19.021 17.8043 18.55 18 18 18H9ZM5 22C4.45 22 3.979 21.8043 3.587 21.413C3.19567 21.021 3 20.55 3 20V7C3 6.71667 3.096 6.479 3.288 6.287C3.47933 6.09567 3.71667 6 4 6C4.28333 6 4.521 6.09567 4.713 6.287C4.90433 6.479 5 6.71667 5 7V20H15C15.2833 20 15.521 20.096 15.713 20.288C15.9043 20.4793 16 20.7167 16 21C16 21.2833 15.9043 21.5207 15.713 21.712C15.521 21.904 15.2833 22 15 22H5Z" fill="white"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('cabinet.layouts.footer')
        </div>
    </div>

@endsection
