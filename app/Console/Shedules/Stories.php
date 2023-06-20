<?php

use Illuminate\Console\Scheduling\Schedule;

use App\Models\Subscribe;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;
use App\Models\StoriesSettings;
use App\Models\StoriesStory;


$schedule->call(function () {
    $allStories = Stories::all();
    foreach ($allStories as $stories) {
        $accountId = $stories->account_id;
        $groupId = $stories->group_id;
        $vkAccount = VkAccount::where('id', '=', $accountId)->first();
        $vkGroup = ($groupId > 0) ? VkGroup::where('group_id', '=', $groupId)->first() : null;
        $userId = $vkAccount->uid;
        $userSubscribe = Subscribe::where('uid', '=', $userId)->first();
        if($userSubscribe){
            if($userSubscribe->sub_status === 1){
                $subscribePackage = unserialize($userSubscribe->sub_config)['vk']['stories'];
                if($subscribePackage['type1']){
                    
                }
            }
        }
    }
})->everyMinute();