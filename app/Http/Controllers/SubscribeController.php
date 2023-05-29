<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{
    public function subscribe(Request $request)
    {

        $ubalance = Auth::user()->UserInfo->balance;
        $uid = Auth::user()->id;

        if( $request->price > $ubalance ){
            return response()->json([
                'errors' => [
                    'no money' => [
                        'title' => 'Недостаточно средств',
                        'message' => 'На балансе недостаточно средств, попробуйте оплатить подписку через FREEKASSA.'
                    ]
                ]
            ], 422);
        }

        $end_date = date("Y-m-d", strtotime("+1 month"));

        $sub_config = array(
            'vk' => array(
                'stream' => array([
                    'type1' => $request->streamType1 ? $request->streamType1 : 0,
                    'type2' => $request->streamType2 ? $request->streamType2 : 0,
                    'type3' => $request->streamType3 ? $request->streamType3 : 0
                ]),
                'story' => array([
                    'type1' => $request->storiesType1 ? $request->storiesType1 : 0,
                    'type2' => $request->storiesType2 ? $request->storiesType2 : 0,
                    'type3' => $request->storiesType3 ? $request->storiesType3 : 0
                ]),
                'posts' => array([
                    'type1' => $request->postsType1 ? $request->postsType1 : 0,
                    'type2' => $request->postsType2 ? $request->postsType2 : 0,
                    'type3' => $request->postsType3 ? $request->postsType3 : 0
                ]),
                'friends' => array([
                    'type1' => $request->friendsType1 ? $request->friendsType1 : 0,
                    'type2' => $request->friendsType2 ? $request->friendsType2 : 0,
                    'type3' => $request->friendsType3 ? $request->friendsType3 : 0
                ]),
            ),
        );

        // При добавление tg добавить условия на смену чисто под tg и когда обе подписки активны - all
        $sub_alt = 'vk';

        Subscribe::create([
            'uid'        => $uid,
            'sub_config' => serialize($sub_config),
            'sub_status' => 1,
            'sub_alt'    => $sub_alt,
            'end_date'   => $end_date
        ]);



        return response()->json([
            'from'      => 'subscribe',
            'title'     => 'Поздравляем!',
            'message'   => 'Вы успешно приобрели подписку.<br> Страница автоматически перегрузится, чтобы изменения применились.'
        ]);
    }
}
