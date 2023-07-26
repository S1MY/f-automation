<?php

namespace App\Http\Controllers;

use App\Models\Offers;
use App\Models\Promocodes;
use App\Models\Subscribe;
use App\Models\User;
use App\Models\UserInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Operation;

use App\Models\Actions;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;
use App\Models\StoriesStory;

use App\Models\Post;
use App\Models\PostStory;
use App\Models\PostSettings;

use App\Models\Stream;
use App\Models\StreamStory;
use App\Models\StreamSettings;
use App\Models\StreamComments;

use App\Models\TgAccount;
use App\Models\TgChannel;
use App\Models\TgStream;
use App\Models\TgPost;

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
        $testPeriod = Actions::where('id', 1)->first();
        return view('cabinet.actions', compact('user', 'testPeriod'));
    }

    public function subscribe(){
        $user = Auth::user();

        return view('cabinet.subscribe', compact('user'));
    }

    public function streams(){
        $user = Auth::user();

        $currentPage = route('streams');
        $VkAccounts = VkAccount::where('uid', $user->id)
            ->get();
        $VkGroups = VkGroup::where('uid', $user->id)
                ->get();
        $allStreams = Stream::join('vk_accounts', 'streams.account_id', '=', 'vk_accounts.id')
        ->leftJoin('vk_groups', function ($join) {
            $join->on('streams.group_id', '=', 'vk_groups.group_id')
                ->on('streams.account_id', '=', 'vk_groups.account_id');
        })
        ->leftJoin('stream_settings', function ($join) {
            $join->on('streams.id', '=', 'stream_settings.stream_id');
        })
        ->orderBy('id', 'desc')
        ->whereIn('streams.account_id', $VkAccounts->pluck('id'))
        ->orWhere(function ($query) use ($VkGroups) {
            foreach ($VkGroups as $group) {
                $query->orWhere(function ($subquery) use ($group) {
                    $subquery->where('streams.account_id', $group->account_id)
                        ->where(function ($q) use ($group) {
                            $q->where('streams.group_id', $group->group_id)
                                ->orWhere('streams.group_id', 0);
                        });
                });
            }
        })
        ->select([
            'streams.*',
            'vk_accounts.name as vk_account_name',
            'vk_groups.name as vk_group_name',
            'stream_settings.type as type',
            'stream_settings.interval as interval',
            'stream_settings.date as date',
            'stream_settings.daily as daily'
        ])
        ->paginate(4, ['*'], 'all-streams-page');

        return view('cabinet.vk.streams', compact('user', 'allStreams', 'currentPage'));
    }

    public function streamInfo($myPageId){
        $user = Auth::user();
        $currentPage = route('streamInfo', ['myPageId' => $myPageId]);
        $stream = Stream::find($myPageId);
        $VkAccount = VkAccount::where('uid', $user->id)->where('id', $stream->account_id)->get();
        $streamSettings = StreamSettings::where('stream_id', $myPageId)->get()->first();
        $streamInfo = StreamStory::join('stream_settings', 'stream_stories.stream_id', '=', 'stream_settings.stream_id')
        ->orderBy('id', 'desc')
        ->where('stream_stories.stream_id', $myPageId)
        ->select([
            'stream_stories.*',
            'stream_settings.type as type',
            'stream_settings.interval as interval',
            'stream_settings.date as date',
            'stream_settings.daily as daily'
        ])
        ->paginate(4, ['*'], 'stream-info-page');
        if(count($VkAccount) == 1){
            return view('cabinet.vk.stream-info', compact('user', 'stream', 'streamSettings', 'streamInfo', 'currentPage'));
        }else{
            return view('cabinet.home', compact('user'));
        }
    }

    public function streamComments($myPageId, $myCommentId){
        $user = Auth::user();
        $currentPage = route('streamComments', ['myPageId' => $myPageId, 'myCommentId' => $myCommentId]);
        $stream = Stream::find($myPageId);
        $streamStory = streamStory::find($myCommentId);
        $VkAccount = VkAccount::where('uid', $user->id)->where('id', $stream->account_id)->get();
        $streamSettings = StreamSettings::where('stream_id', $myPageId)->get()->first();
        $streamComments = StreamComments::join('stream_stories', 'stream_comments.stream_story_id', '=', 'stream_stories.id')
        ->orderBy('id', 'desc')
        ->where('stream_comments.stream_story_id', $myCommentId)
        ->select([
            'stream_comments.*'
        ])
        ->paginate(4, ['*'], 'stream-comments-page');
        if(count($VkAccount) == 1){
            return view('cabinet.vk.stream-comments', compact('user', 'stream', 'streamSettings', 'streamStory', 'streamComments', 'currentPage'));
        }else{
            return view('cabinet.home', compact('user'));
        }
    }

    public function posts(){
        $user = Auth::user();

        $currentPage = route('posts');
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
            $join->on('posts.id', '=', 'post_settings.post_id');
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

        return view('cabinet.vk.posts', compact('user', 'allPosts', 'currentPage'));
    }
    public function postsInfo($myPageId){
        $user = Auth::user();
        $currentPage = route('postsInfo', ['myPageId' => $myPageId]);
        $post = Post::find($myPageId);
        $VkAccount = VkAccount::where('uid', $user->id)->where('id', $post->account_id)->get();
        $postSettings = PostSettings::where('posd_id', $myPageId)->get()->first();
        $postInfo = PostStory::join('post_settings', 'post_stories.post_id', '=', 'post_settings.post_id')
        ->orderBy('id', 'desc')
        ->where('post_stories.post_id', $myPageId)
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
            return view('cabinet.vk.post-info', compact('user', 'post', 'postSettings', 'postInfo', 'currentPage'));
        }else{
            return view('cabinet.home', compact('user'));
        }
    }

    public function stories(){
        $user = Auth::user();

        $currentPage = route('stories');

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
            $join->on('stories.id', '=', 'stories_settings.stories_id');
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
        return view('cabinet.vk.stories', compact('user', 'allStories', 'currentPage'));
    }

    public function storiesInfo($myPageId){
        $user = Auth::user();
        $currentPage = route('storiesInfo', ['myPageId' => $myPageId]);
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
            return view('cabinet.vk.stories-info', compact('user', 'stories', 'storiesInfo', 'currentPage'));
        }else{
            return view('cabinet.home', compact('user'));
        }
    }
    public function tg_streams(){
        $user = Auth::user();

        $currentPage = route('tg_streams');
        $tgAccount = TgAccount::where('uid', $user->id)->get();
        $allStreams = TgStream::join('tg_channels', 'tg_streams.channel_id', '=', 'tg_channels.id')
        ->leftJoin('tg_stream_settings', 'tg_streams.id', '=', 'tg_stream_settings.stream_id')
        ->orderBy('id', 'desc')
        ->whereIn('tg_streams.account_id', $tgAccount->pluck('id'))
        ->select([
            'tg_streams.*',
            'tg_channels.title as tg_channels_name',
            'tg_stream_settings.type as type',
            'tg_stream_settings.interval as interval',
            'tg_stream_settings.date as date',
            'tg_stream_settings.daily as daily'
        ])
        ->paginate(4, ['*'], 'all-tg_streams-page');

        return view('cabinet.tg.streams', compact('user', 'allStreams', 'currentPage'));
    }
    public function tg_posts(){
        $user = Auth::user();

        $currentPage = route('tg_posts');
        $tgAccount = TgAccount::where('uid', $user->id)->get();
        $allPosts = TgPost::join('tg_channels', 'tg_posts.channel_id', '=', 'tg_channels.id')
        ->leftJoin('tg_post_settings', 'tg_posts.id', '=', 'tg_post_settings.post_id')
        ->orderBy('id', 'desc')
        ->whereIn('tg_posts.account_id', $tgAccount->pluck('id'))
        ->select([
            'tg_posts.*',
            'tg_channels.title as tg_channels_name',
            'tg_post_settings.type as type',
            'tg_post_settings.interval as interval',
            'tg_post_settings.date as date',
            'tg_post_settings.daily as daily'
        ])
        ->paginate(4, ['*'], 'all-tg_posts-page');

        return view('cabinet.tg.posts', compact('user', 'allPosts', 'currentPage'));
    }

    public function partners(){
        $user = Auth::user();
        $currentPage = route('partners');
        $allPartners = UserInfo::join('users', 'user_infos.uid', '=', 'users.id')
        ->leftJoin('vk_accounts', function ($join) {
            $join->on('users.id', '=', 'vk_accounts.uid');
        })
        ->orderBy('id', 'desc')
        ->where('user_infos.partner', $user->login)
        ->select([
            'user_infos.*',
            'users.login as login',
            'users.created_at as created_at',
            'vk_accounts.link as link',
        ])
        ->limit(1)
        ->paginate(10,['*'], 'all-partners-page');
        $allOperations = [];
        foreach ($allPartners as $partner) {
            $amount = 0;
            $counter = 0;
            $operations = Operation::where('uid', $partner->uid)->get();
            foreach ($operations as $operation) {
                $counter++;
                $multiplier = ($counter % 50 === 0) ? 0.5 : 0.1;
                $amount += $operation->amount * $multiplier;
            }
            if (count($operations) === 0) {
                $allOperations[$partner->uid] = 0;
            } else {
                $allOperations[$partner->uid] = $amount;
            }
        }
        
        return view('cabinet.partners', compact('user', 'allPartners', 'allOperations', 'currentPage'));
    }

    public function guide(){
        $user = Auth::user();

        return view('cabinet.guide', compact('user'));
    }

    public function offers(){
        $user = Auth::user();
        $currentPage = route('offers');

        $allOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
        ->leftJoin('vk_accounts', function ($join) {
            $join->on('offers.uid', '=', 'vk_accounts.uid')
                ->whereRaw('vk_accounts.id = (SELECT MIN(id) FROM vk_accounts)');
        })
        ->where('published', '=', 1)
        ->where('for_dev', '!=', 1)
        ->where('votes', 'NOT LIKE', '%i:%;i:'.Auth::user()->id.';%')
        ->orderBy('id', 'desc')
        ->select([
            'offers.*',
            'u.login as user_login',
            'vk_accounts.name as account_name',
            'vk_accounts.link as account_link',
            'vk_accounts.avatar as account_avatar'
        ])
        ->paginate(6, ['*'], 'all-offers-page');

        $completeOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
        ->leftJoin('vk_accounts', function ($join) {
            $join->on('offers.uid', '=', 'vk_accounts.uid')
                ->whereRaw('vk_accounts.id = (SELECT MIN(id) FROM vk_accounts)');
        })
        ->where('published', '=', 2)
        ->where('for_dev', '!=', 1)
        ->orderBy('id', 'desc')
        ->select([
            'offers.*',
            'u.login as user_login',
            'vk_accounts.name as account_name',
            'vk_accounts.link as account_link',
            'vk_accounts.avatar as account_avatar'
        ])
        ->paginate(6, ['*'], 'complete-offers-page');

        $likedOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
        ->leftJoin('vk_accounts', function ($join) {
            $join->on('offers.uid', '=', 'vk_accounts.uid')
                ->whereRaw('vk_accounts.id = (SELECT MIN(id) FROM vk_accounts)');
        })
        ->select('u.login', 'offer', 'offers.id', 'published', 'uid', 'for_dev', 'votes', 'need')
        ->where('published', '=', 1)
        ->where('for_dev', '!=', 1)
        ->where('votes', 'LIKE', '%i:%;i:' . Auth::user()->id . ';%')
        ->orderBy('id', 'desc')
        ->select([
            'offers.*',
            'u.login as user_login',
            'vk_accounts.name as account_name',
            'vk_accounts.link as account_link',
            'vk_accounts.avatar as account_avatar'
        ])
        ->paginate(6, ['*'], 'liked-offers-page');

        $meOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
        ->leftJoin('vk_accounts', function ($join) {
            $join->on('offers.uid', '=', 'vk_accounts.uid')
                ->whereRaw('vk_accounts.id = (SELECT MIN(id) FROM vk_accounts)');
        })
        ->where('offers.uid', '=', Auth::user()->id)
        ->orderBy('id', 'desc')
        ->select([
            'offers.*',
            'u.login as user_login',
            'vk_accounts.name as account_name',
            'vk_accounts.link as account_link',
            'vk_accounts.avatar as account_avatar'
        ])
        ->paginate(6, ['*'], 'me-offers-page');

        return view('cabinet.offers', compact('user', 'allOffers', 'meOffers', 'likedOffers', 'completeOffers', 'currentPage'));
    }

    public function admin(Request $request){
        $user = Auth::user();
        $currentPage = route('admin');

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
        // $VKSubs = Subscribe::where('sub_alt', '=', 'vk')->count();
        // $VKGroupsSubs = Subscribe::where('sub_alt', '=', 'vkgroup')->count();
        // $TGSubs = Subscribe::where('sub_alt', '=', 'tg')->count();
        $VKSubs = 1;
        $VKGroupsSubs = 2;
        $TGSubs = 3;

        return view('mainPages.admin', compact( 'user', 'promocodes', 'todayUser',
                                                'countUser', 'countUserNotEmail', 'activeSubs',
                                                'VKSubs', 'VKGroupsSubs', 'TGSubs', 'currentPage',
                                                'offers'));
    }
}
