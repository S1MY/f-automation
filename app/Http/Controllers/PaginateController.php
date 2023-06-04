<?php

namespace App\Http\Controllers;

use App\Models\Offers;
use App\Models\VkAccount;
use App\Models\Promocodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaginateController extends Controller
{
    public function paginate(Request $request){

        $page_id = explode('-page=',explode('?', $request->page)[1])[0];
        $page_num = explode('=', $request->page)[1];

        switch ($page_id) {
            case 'promo':

                $promocodes = Promocodes::orderBy('id', 'DESC')->paginate(5, ['*'], 'promo-page', $page_num );
                $curentPage = route('admin');

                return view('load.promocodes-load', compact('promocodes', 'curentPage'));

                break;

            case 'all-offers':

                $allOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
                            ->select('u.login', 'offer', 'offers.id', 'published', 'uid', 'for_dev', 'votes', 'need')
                            ->where('published', '=', 6)
                            ->where('uid', '!=', Auth::user()->id)
                            ->where('votes', 'NOT LIKE', '%i:%;i:'.Auth::user()->id.';%')
                            ->paginate(1, ['*'], 'all-offers-page', $page_num);

                $curentPage = route('offers');

                return view('load.all-offers-load', compact('allOffers', 'curentPage'));

                break;

            case 'liked-offers':

                $likedOffers = Offers::join('users as u', 'offers.uid', '=', 'u.id')
                           ->select('u.login', 'offer', 'offers.id', 'published', 'uid', 'for_dev', 'votes', 'need')
                           ->where('published', '=', 1)
                           ->where('uid', '!=', Auth::user()->id)
                           ->where('votes', 'LIKE', '%i:%;i:'.Auth::user()->id.';%')
                           ->paginate(6, ['*'], 'liked-offers-page');

                $curentPage = route('offers');

                return view('load.all-offers-load', compact('likedOffers', 'curentPage'));

                break;

            case 'all-stories':

                $allStories = VkAccount::join('users as u', 'vk_accounts.uid', '=', 'u.id')
                            ->select('vk_accounts.id');  
                            
                $allStories = route('vk/stories');
                // return view('load.all-stories-load', compact('allStories', 1));

                break;

            default:

                return response()->json([
                    'from'      => 'paginator',
                    'title'     => 'Произошла ошибка',
                    'message'   => 'Данной страницы не существует...'
                ]);

                break;
        }

    }
}
