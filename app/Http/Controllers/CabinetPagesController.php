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

        return view('cabinet.vk.posts', compact('user'));
    }

    public function stories(){
        $user = Auth::user();

        $curentPage = route('vk/stories');

        $VkAccounts = VkAccount::where('uid', $user->id)
            ->get();
        $VkGroups = VkGroup::where('uid', $user->id)
            ->get();
        $allStories = Stories::join('vk_accounts', 'stories.account_id', '=', 'vk_accounts.id')
        ->leftJoin('vk_groups', function ($join) {
            $join->on('stories.group_id', '=', 'vk_groups.id')
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
            'vk_groups.group_id as vk_group_id',
            'vk_groups.name as vk_group_name'
        ])
        ->paginate(1, ['*'], 'all-stories');
        return view('cabinet.vk.stories', compact('user', 'allStories', 'curentPage'));
    }

    public function storiesInfo(){
        $user = Auth::user();

        return view('cabinet.vk.stories-info', compact('user'));
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
