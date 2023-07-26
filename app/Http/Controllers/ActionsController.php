<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Subscribe;
use App\Models\Actions;

use App\Models\VkAccount;
use App\Models\VkGroup;

class ActionsController extends Controller
{
    public function testPeriod(Request $request)
    {
        $uid = Auth::user()->id;
        $getAction = Actions::where('id', '=', 1)->first();
        if($getAction->users_join === NULL){
            $getAction->users_join = serialize([]);
            $getAction->save();
        }
        if(count(Auth::user()->VkAccount) < 1){
            return $this->generateJsonResponse('error', 'Ошибка', 'Требуется 1 привязанный ВК аккаунт');
        }
        if(count(Auth::user()->TgAccount) < 1){
            return $this->generateJsonResponse('error', 'Ошибка', 'Требуется 1 привязанный ТГ аккаунт');
        }
        $actionUsers = unserialize($getAction->users_join);
    }
}
