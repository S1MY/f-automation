<?php

namespace App\Http\Controllers;

use App\Models\Offers;
use App\Models\Promocodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\VkAccount;
use App\Models\VkGroup;
use App\Models\Stories;

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
                    ->paginate(1, ['*'], 'all-offers-page', $page_num);

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
                        'vk_groups.name as vk_group_name'
                    ])
                    ->paginate(1, ['*'], 'all-stories-page', $page_num);

                $curentPage = route('stories');
                return view('load.all-stories-load', compact('allStories', 'curentPage'));

                break;

            case 'account-stories':
                $VkAccounts = VkAccount::where('id', $accountId)
                    ->get();
                $allStories = Stories::join('vk_accounts', 'stories.account_id', '=', $accountId)
                    ->where('stories.group_id', '=', 0)
                    ->select([
                        'stories.*',
                        'vk_accounts.name as vk_account_name'
                    ])
                    ->paginate(1, ['*'], 'account-stories-page', $page_num);
                $curentPage = route('stories');
                return view('load.all-stories-load', compact('allStories', 'curentPage'));

                break;

            case 'group-stories':
                $VkGroups = VkGroup::where('group_id', $groupId)
                    ->get();
                $allStories = Stories::join('vk_groups', 'stories.account_id', '=', $VkGroups->account_id)
                    ->where('stories.group_id', '=', $groupId)
                    ->select([
                        'stories.*',
                        'vk_groups.name vk_group_name'
                    ])
                    ->paginate(1, ['*'], 'group-stories-page', $page_num);
                $curentPage = route('stories');
                return view('load.all-stories-load', compact('allStories', 'curentPage'));

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