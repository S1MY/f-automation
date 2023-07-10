<?php

namespace App\Http\Controllers;

use App\Models\Offers;
use App\Models\Promocodes;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function setPromocodes(Request $request)
    {

        $getNamePromo = Promocodes::where('code', '=', $request->promo)->get();

        if( $getNamePromo->count() > 0 ){
            return response()->json([
                'errors' => [
                    'promo' => 'Данный промокод уже используется...'
                ]
            ], 422);
        }

        Promocodes::create([
            'code'   => $request->promo,
            'amount' => $request->amount,
            'uses'   => $request->count
        ]);

        // return $create;

        return response()->json([
            'from'      => 'pomocodes',
            'title'     => 'ГудЖопа',
            'message'   => 'Промокод создан.'
        ]);
    }

    public function offerUpdate(Request $request){
        $offerID = $request->offer_id;
        $votes = $request->votes;
        $status = $request->offer_status;

        if( empty($votes) && $votes !== 0){
            return response()->json([
                'errors' => [
                    'votes' => 'Не указано количество голосов'
                ]
            ], 422);
        }
        $offer = Offers::where('id', '=', $offerID)->first();
        $offer->need = $votes;
        $offer->published = $status;
        if($offer->for_dev === 1 && $status === 1){
            $offer->need = 0;
            $offer->published = 2;
        }
        $offer->save();

        $message = ( $status == 1 ) ? 'Оффер опубликован' :'Оффер отклонён';

        return response()->json([
            'from'      => 'offers',
            'offer_id'  => 'offer-id-'.$offerID,
            'status'    => $status,
            'title'     => 'ГудЖопа',
            'message'   => $message
        ]);
    }
}
