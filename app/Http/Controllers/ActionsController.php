<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Subscribe;
use App\Models\Actions;

use App\Models\VkAccount;
use App\Models\TgAccount;

class ActionsController extends Controller
{
    public function testPeriod(Request $request)
    {
        $uid = Auth::user()->id;
        $getAction = Actions::where('id', '=', 1)->first();
        if($getAction->users_join === NULL){
            $empty = [
                'users' => [],
                'vk' => [],
                'tg' => []
            ];
            $getAction->users_join = serialize($empty);
            $getAction->save();
        }
        if(count(Auth::user()->VkAccount) > 1){
            return $this->generateJsonResponse('error', 'Ошибка', 'Требуется не более 1 ВК Аккаунта');
        }
        if(count(Auth::user()->VkAccount) < 1){
            return $this->generateJsonResponse('error', 'Ошибка', 'Требуется 1 привязанный ВК Аккаунт');
        }
        if(count(Auth::user()->TgAccount) < 1){
            return $this->generateJsonResponse('error', 'Ошибка', 'Требуется 1 привязанный ТГ Аккаунт');
        }
        $actionUsers = unserialize($getAction->users_join);
        if (!in_array($uid, $actionUsers['users'])) {
            array_push($actionUsers['users'], $uid);
        } else {
            return $this->generateJsonResponse('error', 'Ошибка', 'Данный F-Аккаунт уже использовал тестовый период!');
        }
        $vkAccount = VkAccount::where('uid', '=', $uid)->first();
        if (!in_array($vkAccount->link, $actionUsers['vk'])) {
            array_push($actionUsers['vk'], $vkAccount->link);
        } else {
            return $this->generateJsonResponse('error', 'Ошибка', 'Привязанный ВК Аккаунт уже использовал тестовый период!');
        }
        $tgAccount = TgAccount::where('uid', '=', $uid)->first();
        if (!in_array($tgAccount->phone, $actionUsers['tg'])) {
            array_push($actionUsers['tg'], $tgAccount->phone);
        } else {
            return $this->generateJsonResponse('error', 'Ошибка', 'Привязанный ТГ Аккаунт уже использовал тестовый период!');
        }
        $getAction->users_join = serialize($actionUsers);
        $getAction->save();
        $endDate = date('Y-m-d H:i:s', strtotime('+4 days'));
        $subConfig = [
            'vk' => [
                'streams' => [
                    'type1' => 1,
                    'type2' => 1,
                    'type3' => 1
                ],
                'stories' => [
                    'type1' => 1,
                    'type2' => 1,
                    'type3' => 1
                ],
                'posts' => [
                    'type1' => 1,
                    'type2' => 1,
                    'type3' => 1
                ]
            ],
            'tg' => [
                'streams' => [
                    'type1' => 1,
                    'type2' => 1,
                ],
                'posts' => [
                    'type1' => 1
                ]
            ]
        ];
        $getSubscribe = Subscribe::where('uid', '=', $uid)->first();
        if($getSubscribe){
            if($getSubscribe->sub_status === 1){
                return $this->generateJsonResponse('error', 'Ошибка', 'У вас уже есть активный пакет!');
            }else{
                $getSubscribe->sub_config = serialize($subConfig);
                $getSubscribe->sub_status = 1;
                $getSubscribe->end_date = $endDate;
                $getSubscribe->is_test = 1;
                $getSubscribe->save();
            }
        }else{
            Subscribe::create([
                'uid'        => $uid,
                'sub_config' => serialize($subConfig),
                'sub_status' => 1,
                'end_date'   => $endDate,
                'is_test'   => 1
            ]);
        }
        return $this->generateJsonResponse('success', 'Поздравляем!', 'Вы успешно активировали тестовый период');
    }
}
