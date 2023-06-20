<?php

use Illuminate\Console\Scheduling\Schedule;
use Carbon\Carbon;

use App\Models\Subscribe;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;
use App\Models\StoriesStory;


$schedule->call(function () {
    $allStories = Stories::all();
    foreach ($allStories as $stories) {
        if($stories->active === 1){
            $updatedAt = Carbon::createFromFormat('Y-m-d H:i:s', $stories->updated_at, 'Europe/Moscow')->startOfMinute();
            $updatedAtNext = $updatedAt->copy()->addDay()->startOfMinute();
            if ($updatedAtNext === Carbon::now('Europe/Moscow')->startOfMinute()) {
                $accountId = $stories->account_id;
                $groupId = $stories->group_id;
                $vkAccount = VkAccount::where('id', '=', $accountId)->first();
                $vkToken = $vkAccount->token;
                $vkGroup = ($groupId > 0) ? VkGroup::where('group_id', '=', $groupId)->first() : null;
                $userId = $vkAccount->uid;
                $userSubscribe = Subscribe::where('uid', '=', $userId)->first();
                if($userSubscribe){
                    if($userSubscribe->sub_status === 1){
                        $subscribePackage = unserialize($userSubscribe->sub_config)['vk']['stories'];
                        if($subscribePackage['type1'] === 1){
                            if($vkGroup && $subscribePackage['type2'] !== 1){
                                die();
                            }
                            if(count(unserialize($stories->upload_result)) > 1 && $subscribePackage['type3'] !== 1){
                                die();
                            }
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
                                    die();
                                }
                                $storiesContent = $storiesGet->getBody()->getContents();
                                $storiesJSON = json_decode($storiesContent);
                                if (isset($storiesJSON->error)) {
                                    die();
                                }
                                $stories->updated_at = Carbon::now('Europe/Moscow')->startOfMinute()->format('Y-m-d H:i:s');
                                $stories-save();
                                $storiesLast = StoriesStory::latest()->first();
                            }
                        }
                    }else{
                        $stories->active = 0;
                        $stories-save();
                    }
                }
            }elseif ($updatedAtNext->subMinutes(5) === Carbon::now('Europe/Moscow')->startOfMinute()) {
                # code...
            }
        }
    }
})->everyMinute();