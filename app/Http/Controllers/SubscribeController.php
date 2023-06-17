<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{
    public function subscribe(Request $request)
    {
        $rules = [
            'price' => 'integer',
            'promoChecker' => 'integer|in:0,1',
            'promocode' => 'sting',
        ];
        $messages = [
            'price.integer' => 'Сумма не является числом',
            'promoChecker.integer' => 'Значение активного промокода не является числом',
            'promoChecker.in' => 'Значение активного промокода 0 или 1',
            'promocode.sting' => 'Промокод должен быть строкой',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('error', 'Ошибка заполнения', $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();

        $ubalance = Auth::user()->UserInfo->balance;
        $uid = Auth::user()->id;

        if( $validatedData['price'] > $ubalance ){
            return $this->generateJsonResponse('error', 'Недостаточно средств', 'На балансе недостаточно средств, попробуйте оплатить подписку через FREEKASSA.');
        }

        $end_date = date("Y-m-d", strtotime("+1 month"));

        $sub_config = [
            'vk' => [
                'streams' => [
                    'type1' => ($request->streamType1 === 1) ? $request->streamType1 : 0,
                    'type2' => ($request->streamType2 === 1) ? $request->streamType2 : 0,
                    'type3' => ($request->streamType3 === 1) ? $request->streamType3 : 0
                ],
                'stories' => [
                    'type1' => ($request->storiesType1 === 1) ? $request->storiesType1 : 0,
                    'type2' => ($request->storiesType2 === 1) ? $request->storiesType2 : 0,
                    'type3' => ($request->storiesType3 === 1) ? $request->storiesType3 : 0
                ],
                'posts' => [
                    'type1' => ($request->postType1 === 1) ? $request->postType1 : 0,
                    'type2' => ($request->postType2 === 1) ? $request->postType2 : 0,
                    'type3' => ($request->postType3 === 1) ? $request->postType3 : 0
                ],
                'friends' => [
                    'type1' => ($request->friendsType1 === 1) ? $request->friendsType1 : 0,
                    'type2' => ($request->friendsType2 === 1) ? $request->friendsType2 : 0,
                    'type3' => ($request->friendsType3 === 1) ? $request->friendsType3 : 0
                ],
            ],
        ];

        Subscribe::create([
            'uid'        => $uid,
            'sub_config' => serialize($sub_config),
            'sub_old'    => serialize($sub_config),
            'sub_status' => 1,
            'end_date'   => $end_date
        ]);
        return $this->generateJsonResponse('success', 'Поздравляем!', 'Вы успешно приобрели подписку.<br> Страница автоматически перегрузится, чтобы изменения применились.');
    }
    private function generateJsonResponse($type, $title, $message)
    {
        return response()->json([
            'status' => $type,
            'title' => $title,
            'message' => $message
        ]);
    }
}
