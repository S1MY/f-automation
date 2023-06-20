<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Subscribe;
use App\Models\Promocodes;

class SubscribeController extends Controller
{
    public function subscribe(Request $request)
    {
        $rules = [
            'price' => 'integer',
            'promoChecker' => 'integer|in:0,1',
            'promocode' => 'string',
        ];
        $messages = [
            'price.integer' => 'Сумма не является числом',
            'promoChecker.integer' => 'Значение активного промокода не является числом',
            'promoChecker.in' => 'Значение активного промокода 0 или 1',
            'promocode.string' => 'Промокод должен быть строкой',
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
        $clientPriceNumber = (is_numeric($validatedData['price'])) ? intval($validatedData['price']) : 'error';
        if($clientPriceNumber === 'error'){
            return $this->generateJsonResponse('error', 'Ошибка суммы', 'Сумма должна являться целым числом');
        }
        if($clientPriceNumber === 0){
            return $this->generateJsonResponse('error', 'Пустой пакет', 'С пустым пакетом вы далеко не уедете. Пересоберите его пожалуйста!');
        }
        if( $clientPriceNumber > $ubalance ){
            return $this->generateJsonResponse('error', 'Недостаточно средств', 'На балансе недостаточно средств, попробуйте оплатить подписку через FREEKASSA.');
        }
        $endDate = date("Y-m-d", strtotime("+1 month"));
        $subConfig = [
            'vk' => [
                'streams' => [
                    'type1' => (is_numeric($request->streamType1)) ? intval($request->streamType1) : 0,
                    'type2' => (is_numeric($request->streamType2)) ? intval($request->streamType2) : 0,
                    'type3' => (is_numeric($request->streamType3)) ? intval($request->streamType3) : 0
                ],
                'stories' => [
                    'type1' => (is_numeric($request->storiesType1)) ? intval($request->storiesType1) : 0,
                    'type2' => (is_numeric($request->storiesType2)) ? intval($request->storiesType2) : 0,
                    'type3' => (is_numeric($request->storiesType3)) ? intval($request->storiesType3) : 0
                ],
                'posts' => [
                    'type1' => (is_numeric($request->postType1)) ? intval($request->postType1) : 0,
                    'type2' => (is_numeric($request->postType2)) ? intval($request->postType2) : 0,
                    'type3' => (is_numeric($request->postType3)) ? intval($request->postType3) : 0
                ]
            ]
        ];
        $packagePrices = [
            'vk' => [
                'streams' => [
                    'type1' => 200,
                    'type2' => 200,
                    'type3' => 100
                ],
                'stories' => [
                    'type1' => 100,
                    'type2' => 100,
                    'type3' => 50
                ],
                'posts' => [
                    'type1' => 100,
                    'type2' => 100,
                    'type3' => 50
                ]
            ]
        ];
        $packageParents = [
            'vk' => [
                'streams' => [
                    'type1' => '0',
                    'type2' => 'type1',
                    'type3' => 'type1'
                ],
                'stories' => [
                    'type1' => '0',
                    'type2' => 'type1',
                    'type3' => 'type1'
                ],
                'posts' => [
                    'type1' => '0',
                    'type2' => 'type1',
                    'type3' => 'type1'
                ]
            ]
        ];
        foreach ($subConfig['vk'] as $type => $options) {
            foreach ($options as $subtype => $selected) {
                if ($selected === 1 && $packageParents['vk'][$type][$subtype] !== '0') {
                    $requiredType = $packageParents['vk'][$type][$subtype];
                    if ($subConfig['vk'][$type][$requiredType] !== 1) {
                        return $this->generateJsonResponse('error', 'Неверный пакет!', 'Для функций ВК '.$type.' выбора '.$subtype.' обязателен '.$requiredType);
                    }
                }
            }
        }
        $totalPrice = 0;
        foreach ($subConfig['vk'] as $type => $options) {
            foreach ($options as $subtype => $selected) {
                if ($selected === 1) {
                    $totalPrice += $packagePrices['vk'][$type][$subtype];
                }
            }
        }
        if($totalPrice !== $clientPriceNumber){
            if($validatedData['promoChecker'] < 1){
                return $this->generateJsonResponse('error', 'Ошибка промокода', 'Сумма отправленная с клиента не соответствует значению сервера');
            }
            $getPromocode = Promocodes::where('code', '=', $validatedData['promocode'])->first();
            if (!$getPromocode) {
                return $this->generateJsonResponse('error', 'Ошибка промокода', 'Данный промокод не найден. Проверьте ещё раз!');
            }
            $now = Carbon::now();
            if ($now > Carbon::parse($getPromocode->before) || $getPromocode->active < 1) {
                $getPromocode->active = 0;
                $getPromocode->save();
                return $this->generateJsonResponse('error', 'Ошибка промокода', 'Промокод больше недействителен');
            }
            $usersActivate = unserialize($getPromocode->users_active);
            if(is_array($usersActivate)){
                if (in_array($uid, $usersActivate)) {
                    return $this->generateJsonResponse('error', 'Ошибка промокода', 'Вы уже использовали данный промокод');
                }
            }
            $usersActivate[] = $uid;
            if($getPromocode->uses < 1){
                return $this->generateJsonResponse('error', 'Ошибка промокода', 'У промокода закончилось количество использований!');
            }
            $newTotalPrice = $totalPrice - $getPromocode->amount;
            if($newTotalPrice !== $clientPriceNumber){
                return $this->generateJsonResponse('error', 'Ошибка промокода', 'Сумма отправленная с клиента не соответствует значению сервера');
            }
            $getPromocode->uses = $getPromocode->uses - 1;
            $getPromocode->users_active = serialize($usersActivate);
            $getPromocode->save();
        }
        Auth::user()->UserInfo->balance = $ubalance - $totalPrice;
        Auth::user()->UserInfo->save();

        Subscribe::create([
            'uid'        => $uid,
            'sub_config' => serialize($subConfig),
            'sub_status' => 1,
            'end_date'   => $endDate
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
