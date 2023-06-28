<?php

namespace App\Http\Controllers;

use App\Models\Offers;
use App\Models\Promocodes;
use App\Models\Subscribe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;
use App\Models\StoriesStory;

use App\Models\Post;
use App\Models\PostStory;

class CabinetPagesController extends Controller
{
    public function settings(){
        $user = Auth::user();

        return view('cabinet.settings', compact('user'));
    }

    public function cabinet(){
        $user = Auth::user();

        return view('cabinet.home', compact('user'));
    }

    public function actions(){
        $user = Auth::user();

        return view('cabinet.actions', compact('user'));
    }

    public function subscribe(){
        $user = Auth::user();

        return view('cabinet.subscribe', compact('user'));
    }

    public function streams(){
        $user = Auth::user();

        return view('cabinet.vk.streams', compact('user'));
    }

    public function streamInfo(){
        $user = Auth::user();

        return view('cabinet.vk.stream-info', compact('user'));
    }

    public function streamComments(){
        $user = Auth::user();

        return view('cabinet.vk.stream-comments', compact('user'));
    }

    public function posts(){
        $user = Auth::user();

        $curentPage = route('posts');
        $VkAccounts = VkAccount::where('uid', $user->id)
            ->get();
        $VkGroups = VkGroup::where('uid', $user->id)
                ->get();
        $allPosts = Post::join('vk_accounts', 'posts.account_id', '=', 'vk_accounts.id')
        ->leftJoin('vk_groups', function ($join) {
            $join->on('posts.group_id', '=', 'vk_groups.group_id')
                ->on('posts.account_id', '=', 'vk_groups.account_id');
        })
        ->leftJoin('post_settings', function ($join) {
            $join->on('posts.id', '=', 'post_settings.post_id')
            ->where('posts.group_id', '<>', 0);
        })
        ->orderBy('id', 'desc')
        ->whereIn('posts.account_id', $VkAccounts->pluck('id'))
        ->orWhere(function ($query) use ($VkGroups) {
            foreach ($VkGroups as $group) {
                $query->orWhere(function ($subquery) use ($group) {
                    $subquery->where('posts.account_id', $group->account_id)
                        ->where(function ($q) use ($group) {
                            $q->where('posts.group_id', $group->group_id)
                                ->orWhere('posts.group_id', 0);
                        });
                });
            }
        })
        ->select([
            'posts.*',
            'vk_accounts.name as vk_account_name',
            'vk_groups.name as vk_group_name',
            'post_settings.friends_only as seetings_friends_only',
            'post_settings.close_comments as seetings_close_comments',
            'post_settings.pin_new as seetings_pin_new',
            'post_settings.remove_old as seetings_remove_old',
            'post_settings.type as type',
            'post_settings.interval as interval',
            'post_settings.date as date',
            'post_settings.daily as daily'
        ])
        ->paginate(4, ['*'], 'all-posts-page');

        return view('cabinet.vk.posts', compact('user', 'allPosts', 'curentPage'));
    }
    public function postsInfo($myPageId){
        $user = Auth::user();
        $curentPage = route('posts', ['myPageId' => $myPageId]);
        $post = Post::find($myPageId);
        $VkAccount = VkAccount::where('uid', $user->id)->where('id', $post->account_id)->get();
        $postInfo = PostStory::where('post_id', $myPageId)->get()
        ->join('post_settings', 'post_stories.post_id', '=', 'post_settings.post_id')
        ->orderBy('id', 'desc')
        ->select([
            'post_stories.*',
            'post_settings.friends_only as friends_only',
            'post_settings.close_comments as close_comments',
            'post_settings.pin_new as pin_new',
            'post_settings.remove_old as remove_old',
            'post_settings.type as type',
            'post_settings.interval as interval',
            'post_settings.date as date',
            'post_settings.daily as daily'
        ])
        ->paginate(4, ['*'], 'posts-info-page');
        if(count($VkAccount) == 1){
            return view('cabinet.vk.post-info', compact('user', 'post', 'postInfo', 'curentPage'));
        }else{
            return view('cabinet.home', compact('user'));
        }
    }

    public function stories(){
        $user = Auth::user();

        $curentPage = route('stories');

        $VkAccounts = VkAccount::where('uid', $user->id)
            ->get();
        $VkGroups = VkGroup::where('uid', $user->id)
            ->get();
        $allStories = Stories::join('vk_accounts', 'stories.account_id', '=', 'vk_accounts.id')
        ->leftJoin('vk_groups', function ($join) {
            $join->on('stories.group_id', '=', 'vk_groups.group_id')
                ->on('stories.account_id', '=', 'vk_groups.account_id');
        })
        ->leftJoin('stories_settings', function ($join) {
            $join->on('stories.id', '=', 'stories_settings.stories_id')
            ->where('stories.group_id', '<>', 0);
        })
        ->orderBy('id', 'desc')
        ->whereIn('stories.account_id', $VkAccounts->pluck('id'))
        ->orWhere(function ($query) use ($VkGroups) {
            foreach ($VkGroups as $group) {
                $query->orWhere(function ($subquery) use ($group) {
                    $subquery->where('stories.account_id', $group->account_id)
                        ->where(function ($q) use ($group) {
                            $q->where('stories.group_id', $group->group_id)
                                ->orWhere('stories.group_id', 0);
                        });
                });
            }
        })
        ->select([
            'stories.*',
            'vk_accounts.name as vk_account_name',
            'vk_groups.name as vk_group_name',
            'stories_settings.link_text as seetings_link_text',
            'stories_settings.link_url as seetings_link_url'
        ])
        ->paginate(4, ['*'], 'all-stories-page');
        return view('cabinet.vk.stories', compact('user', 'allStories', 'curentPage'));
    }

    public function storiesInfo($myPageId){
        $user = Auth::user();
        $curentPage = route('stories', ['myPageId' => $myPageId]);
        $stories = Stories::find($myPageId);
        $VkAccount = VkAccount::where('uid', $user->id)->where('id', $stories->account_id)->get();
        $storiesInfo = StoriesStory::join('stories_settings', 'stories_stories.stories_id', '=', 'stories_settings.stories_id')
        ->orderBy('id', 'desc')
        ->select([
            'stories_stories.*',
            'stories_settings.link_text as link_text',
            'stories_settings.link_url as link_url'
        ])
        ->paginate(4, ['*'], 'stories-info-page');
        if(count($VkAccount) == 1){
            return view('cabinet.vk.stories-info', compact('user', 'stories', 'storiesInfo', 'curentPage'));
        }else{
            return view('cabinet.home', compact('user'));
        }
    }

    public function partners(){
        $user = Auth::user();

        return view('cabinet.partners', compact('user'));
    }

    public function guide(){
        $user = Auth::user();

        return view('cabinet.guide', compact('user'));
    }

    public function offers(){
        $user = Auth::user();
        $curentPage = route('offers');

        $allOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
                           ->select('u.login', 'offer', 'offers.id', 'published', 'uid', 'for_dev', 'votes', 'need')
                           ->where('published', '=', 1)
                           ->where('uid', '!=', Auth::user()->id)
                           ->where('votes', 'NOT LIKE', '%i:%;i:'.Auth::user()->id.';%')
                           ->paginate(6, ['*'], 'all-offers-page');

        $likedOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
                           ->select('u.login', 'offer', 'offers.id', 'published', 'uid', 'for_dev', 'votes', 'need')
                           ->where('published', '=', 1)
                           ->where('uid', '!=', Auth::user()->id)
                           ->where('votes', 'LIKE', '%i:%;i:'.Auth::user()->id.';%')
                           ->paginate(6, ['*'], 'liked-offers-page');

        $meOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
                            ->select('u.login', 'offer', 'offers.id', 'published', 'uid', 'for_dev', 'votes', 'need')
                            ->where('uid', '=', Auth::user()->id)
                            ->paginate(6);

        return view('cabinet.offers', compact('user', 'allOffers', 'meOffers', 'likedOffers', 'curentPage'));
    }

    public function admin(Request $request){
        $user = Auth::user();
        $curentPage = route('admin');

        // Предложения
        $offers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
                         ->select('u.login', 'offer', 'for_dev', 'offers.id', 'offers.id')
                         ->orderBy('id', 'DESC')
                         ->where('published', '=', 0)
                         ->orWhere('published', '=', null)
                         ->get();

        // Промокоды
        $promocodes = Promocodes::orderBy('id', 'DESC')->paginate(5, ['*'], 'promo-page');

        // Статистика
        $todayUser = User::whereDate('created_at', Carbon::today())->count();
        $countUser = User::count();
        $countUserNotEmail = User::where('email_verified_at', '=', null)->count();
        $activeSubs = Subscribe::count();
        $VKSubs = Subscribe::where('sub_alt', '=', 'vk')->count();
        $VKGroupsSubs = Subscribe::where('sub_alt', '=', 'vkgroup')->count();
        $TGSubs = Subscribe::where('sub_alt', '=', 'tg')->count();

        return view('mainPages.admin', compact( 'user', 'promocodes', 'todayUser',
                                                'countUser', 'countUserNotEmail', 'activeSubs',
                                                'VKSubs', 'VKGroupsSubs', 'TGSubs', 'curentPage',
                                                'offers'));
    }
}
