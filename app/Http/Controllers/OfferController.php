<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferRequest;
use App\Models\Offers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function setOffer(OfferRequest $request){

        if( !isset($request->developer) ){
            $for_dev = 0;
        }
        $for_dev = $request->developer;

        Offers::create([
            'uid'     => Auth::user()->id,
            'offer'   => $request->offer,
            'votes'   => serialize(array()),
            'for_dev' => $for_dev,
        ]);

        return response()->json([
            'from'      => 'offers',
            'title'     => 'Ваше предложение принято.',
            'message'   => 'Предложение будет опубликовано, если оно соотвествует нашим правилам.'
        ]);
    }

    public function getVote(Request $request){

        $offer = Offers::where('id', '=', $request->offerId)->first();
        $array = unserialize($offer->votes);

        if ( empty($array) ){
            $array = array(Auth::user()->id);
        }else{
            array_push($array, Auth::user()->id);
        }

        $offer->votes = serialize($array);
        $offer->save();

        return response()->json([
            'from'      => 'offers',
            'title'     => 'Вы уже голосовали!',
            'now'       => count($array),
            'offer_id'  => $request->offerId
        ]);
    }
}
