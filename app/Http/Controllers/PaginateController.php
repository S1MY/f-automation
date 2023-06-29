<?php

namespace App\Http\Controllers;

use App\Models\Offers;
use App\Models\Promocodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;
use App\Models\StoriesStory;

use App\Models\Post;
use App\Models\PostStory;

use App\Models\Stream;
use App\Models\StreamStory;

class PaginateController extends Controller
{
    public function paginate(Request $request)
    {
        if (isset($request->page)) {
            $page_id = explode('-page=', explode('?', $request->page)[1])[0];
            $page_num = explode('=', $request->page)[1];
        } else {
            $page_id = $request->type;
            $page_num = 1;
        }
        $accountId = (isset($request->account_id) ? $request->account_id : '');
        $groupId = (isset($request->group_id) ? $request->group_id : '');
        $myPageId = (isset($request->myPageId) ? $request->myPageId : '');

        switch ($page_id) {
            case 'promo':

                $promocodes = Promocodes::orderBy('id', 'DESC')->paginate(5, ['*'], 'promo-page', $page_num);
                $curentPage = route('admin');

                return view('load.promocodes-load', compact('promocodes', 'curentPage'));

                break;

            case 'all-offers':

                $allOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
                    ->select('u.login', 'offer', 'offers.id', 'published', 'uid', 'for_dev', 'votes', 'need')
                    ->where('published', '=', 6)
                    ->where('uid', '!=', Auth::user()->id)
                    ->where('votes', 'NOT LIKE', '%i:%;i:' . Auth::user()->id . ';%')
                    ->paginate(4, ['*'], 'all-offers-page', $page_num);

                $curentPage = route('offers');

                return view('load.all-offers-load', compact('allOffers', 'curentPage'));

                break;

            case 'liked-offers':

                $likedOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
                    ->select('u.login', 'offer', 'offers.id', 'published', 'uid', 'for_dev', 'votes', 'need')
                    ->where('published', '=', 1)
                    ->where('uid', '!=', Auth::user()->id)
                    ->where('votes', 'LIKE', '%i:%;i:' . Auth::user()->id . ';%')
                    ->paginate(6, ['*'], 'liked-offers-page');

                $curentPage = route('offers');

                return view('load.all-offers-load', compact('likedOffers', 'curentPage'));

                break;

            case 'all-stories':

                $VkAccounts = VkAccount::where('uid', Auth::user()->id)
                    ->get();
                $VkGroups = VkGroup::where('uid', Auth::user()->id)
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
                    ->paginate(4, ['*'], 'all-stories-page', $page_num);

                $curentPage = route('stories');
                return view('load.all-stories-load', compact('allStories', 'curentPage'));

                break;

            case 'account-stories':
                $VkAccounts = VkAccount::where('id', $accountId)
                    ->get();
                $allStories = Stories::join('vk_accounts', 'stories.account_id', '=', 'vk_accounts.id')
                ->orderBy('id', 'desc')
                    ->where('stories.account_id', '=', $VkAccounts->pluck('id'))
                    ->where('stories.group_id', '=', 0)
                    ->select([
                        'stories.*',
                        'vk_accounts.name as vk_account_name'
                    ])
                    ->paginate(4, ['*'], 'account-stories-page', $page_num);
                $curentPage = route('stories');
                return view('load.all-stories-load', compact('allStories', 'curentPage'));

                break;

            case 'group-stories':
                $VkGroups = VkGroup::where('group_id', $groupId)
                    ->get();
                $allStories = Stories::join('vk_groups', 'stories.group_id', '=', 'vk_groups.group_id')
                    ->leftJoin('stories_settings', 'stories.id', '=', 'stories_settings.stories_id')
                    ->orderBy('id', 'desc')
                    ->where('stories.account_id', '=', $VkGroups->pluck('account_id'))
                    ->where('stories.group_id', '=', $VkGroups->pluck('group_id'))
                    ->select([
                        'stories.*',
                        'vk_groups.name as vk_group_name',
                        'stories_settings.link_text as seetings_link_text',
                        'stories_settings.link_url as seetings_link_url'
                    ])
                    ->paginate(4, ['*'], 'group-stories-page', $page_num);
                $curentPage = route('stories');
                return view('load.all-stories-load', compact('allStories', 'curentPage'));

                break;
            case 'stories-info':
                $stories = Stories::where('id', $myPageId)
                    ->get();
                $storiesInfo = StoriesStory::join('stories_settings', 'stories_stories.stories_id', '=', 'stories_settings.stories_id')
                ->orderBy('id', 'desc')
                ->where('stories_stories.stories_id', '=', $stories->pluck('id'))
                ->select([
                    'stories_stories.*',
                    'stories_settings.link_text as link_text',
                    'stories_settings.link_url as link_url'
                ])
                ->paginate(4, ['*'], 'stories-info-page', $page_num);
                $curentPage = route('stories', ['myPageId' => $myPageId]);
                return view('load.stories-info-load', compact('stories', 'storiesInfo', 'curentPage'));
                break;
            case 'all-streams':
                $VkAccounts = VkAccount::where('uid', Auth::user()->id)
                    ->get();
                $VkGroups = VkGroup::where('uid', Auth::user()->id)
                    ->get();
                $allStreams = Stream::join('vk_accounts', 'streams.account_id', '=', 'vk_accounts.id')
                ->leftJoin('vk_groups', function ($join) {
                    $join->on('streams.group_id', '=', 'vk_groups.group_id')
                        ->on('streams.account_id', '=', 'vk_groups.account_id');
                })
                ->leftJoin('stream_settings', function ($join) {
                    $join->on('streams.id', '=', 'stream_settings.stream_id	')
                    ->where('streams.group_id', '<>', 0);
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
                ->paginate(1, ['*'], 'all-streams-page', $page_num);
                $curentPage = route('streams');
                return view('load.all-streams-load', compact('allStreams', 'curentPage'));
                break;
            case 'account-streams':
                $VkAccounts = VkAccount::where('id', $accountId)
                    ->get();
                $allStreams = Stream::join('vk_accounts', 'streams.account_id', '=', 'vk_accounts.id')
                ->leftJoin('stream_settings', 'streams.id', '=', 'stream_settings.post_id')
                ->orderBy('id', 'desc')
                    ->where('streams.account_id', '=', $VkAccounts->pluck('id'))
                    ->where('streams.group_id', '=', 0)
                    ->select([
                        'streams.*',
                        'vk_accounts.name as vk_account_name',
                        'stream_settings.type as type',
                        'stream_settings.interval as interval',
                        'stream_settings.date as date',
                        'stream_settings.daily as daily'
                    ])
                    ->paginate(4, ['*'], 'account-streams-page', $page_num);
                $curentPage = route('streams');
                return view('load.all-streams-load', compact('allStreams', 'curentPage'));

                break;
            case 'group-streams':
                $VkGroups = VkGroup::where('group_id', $groupId)
                    ->get();
                $allStreams = Post::join('vk_groups', 'streams.group_id', '=', 'vk_groups.group_id')
                    ->leftJoin('stream_settings', 'streams.id', '=', 'stream_settings.post_id')
                    ->orderBy('id', 'desc')
                    ->where('streams.account_id', '=', $VkGroups->pluck('account_id'))
                    ->where('streams.group_id', '=', $VkGroups->pluck('group_id'))
                    ->select([
                        'streams.*',
                        'vk_groups.name as vk_group_name',
                        'stream_settings.type as type',
                        'stream_settings.interval as interval',
                        'stream_settings.date as date',
                        'stream_settings.daily as daily'
                    ])
                    ->paginate(4, ['*'], 'group-streams-page', $page_num);
                $curentPage = route('streams');
                return view('load.all-streams-load', compact('allStreams', 'curentPage'));

                break;
            case 'all-posts':
                $VkAccounts = VkAccount::where('uid', Auth::user()->id)
                    ->get();
                $VkGroups = VkGroup::where('uid', Auth::user()->id)
                    ->get();
                $allPosts = Post::join('vk_accounts', 'posts.account_id', '=', 'vk_accounts.id')
                ->leftJoin('vk_groups', function ($join) {
                    $join->on('posts.group_id', '=', 'vk_groups.group_id')
                        ->on('posts.account_id', '=', 'vk_groups.account_id');
                })
                ->leftJoin('post_settings', 'posts.id', '=', 'post_settings.post_id')
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
                ->paginate(4, ['*'], 'all-posts-page', $page_num);

                $curentPage = route('posts');
                return view('load.all-posts-load', compact('allPosts', 'curentPage'));
                break;
            case 'account-posts':
                $VkAccounts = VkAccount::where('id', $accountId)
                    ->get();
                $allPosts = Post::join('vk_accounts', 'posts.account_id', '=', 'vk_accounts.id')
                ->leftJoin('post_settings', 'posts.id', '=', 'post_settings.post_id')
                ->orderBy('id', 'desc')
                    ->where('posts.account_id', '=', $VkAccounts->pluck('id'))
                    ->where('posts.group_id', '=', 0)
                    ->select([
                        'posts.*',
                        'vk_accounts.name as vk_account_name',
                        'post_settings.friends_only as seetings_friends_only',
                        'post_settings.close_comments as seetings_close_comments',
                        'post_settings.pin_new as seetings_pin_new',
                        'post_settings.remove_old as seetings_remove_old',
                        'post_settings.type as type',
                        'post_settings.interval as interval',
                        'post_settings.date as date',
                        'post_settings.daily as daily'
                    ])
                    ->paginate(4, ['*'], 'account-posts-page', $page_num);
                $curentPage = route('posts');
                return view('load.all-posts-load', compact('allPosts', 'curentPage'));

                break;
            case 'group-posts':
                $VkGroups = VkGroup::where('group_id', $groupId)
                    ->get();
                $allPosts = Post::join('vk_groups', 'posts.group_id', '=', 'vk_groups.group_id')
                    ->leftJoin('post_settings', 'posts.id', '=', 'post_settings.post_id')
                    ->orderBy('id', 'desc')
                    ->where('posts.account_id', '=', $VkGroups->pluck('account_id'))
                    ->where('posts.group_id', '=', $VkGroups->pluck('group_id'))
                    ->select([
                        'posts.*',
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
                    ->paginate(4, ['*'], 'group-posts-page', $page_num);
                $curentPage = route('posts');
                return view('load.all-posts-load', compact('allPosts', 'curentPage'));

                break;
            case 'posts-info':
                $post = Post::find($myPageId);
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
                ->paginate(4, ['*'], 'posts-info-page', $page_num);
                $curentPage = route('posts', ['myPageId' => $myPageId]);
                return view('load.posts-info-load', compact('post', 'postInfo', 'curentPage'));
                break;
            default:

                return response()->json([
                    'from' => 'paginator',
                    'title' => 'Произошла ошибка',
                    'message' => 'Данной страницы не существует...'
                ]);

                break;
        }

    }
}