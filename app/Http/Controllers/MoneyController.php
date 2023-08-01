<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use App\Models\Operation;
use App\Models\UserInfo;

class MoneyController extends Controller
{
    public function getLink(Request $request)
    {
        $amount = $request->amount;
        if (is_numeric($amount)) {
            if($amount < 100){
                return $this->generateJsonResponse('error', 'Ошибка пополнения', 'Минимальная сумма для пополнения 100 рублей');
            }
            $newOperation = Operation::create([
                'uid' => Auth::user()->id,
                'amount' => $amount,
                'date_insert' => date('Y-m-d H:i:s')
            ]);
            $getParams = [
                'm' => env('PAYMENT_SHOP_ID'),
                'oa' => $amount,
                'currency' => 'RUB',
                'o' => $newOperation->id,
                'em' => Auth::user()->UserInfo->email,
                'us_uid' => Auth::user()->id
            ];
            ksort($getParams);
            $sign = md5(env('PAYMENT_SHOP_ID').'|'.$amount.'|'.env('PAYMENT_SHOP_SECRET').'|'.$getParams['currency'].'|'.$newOperation->id);
            $getParams['s'] = $sign;
            $queryString = http_build_query($getParams);
            
            $location = 'https://pay.freekassa.ru/?' . $queryString;
            return $this->generateJsonResponse('success', 'Ссылка получена', $location);
        }
    }
    public function moneyCore(Request $request)
    {
        if(!in_array($request->ip(), ['168.119.157.136', '168.119.60.227', '138.201.88.124', '178.154.197.79'])){
            die("hacking attempt!");
        }
        $sign = md5(env('PAYMENT_SHOP_ID').'|'.$request->input('AMOUNT').'|'.env('PAYMENT_SHOP_CORE').'|'.$request->input('MERCHANT_ORDER_ID'));
        if ($sign != $request->input('SIGN')) {
            die('wrong sign');
        }
        $payId = $request->input('intid');
        $userId = $request->input('us_uid');
        $userInfo = UserInfo::where('uid', $userId)->first();
        $userInfo->balance = $userInfo->balance + $amount;
        $userInfo->save();
        $thisOperation = Operation::where('uid', $userId)->latest();
        $thisOperation->pid = $payId;
        $thisOperation->status = 1;
        $thisOperation->save();
        die('YES');
    }
}
