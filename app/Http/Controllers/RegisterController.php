<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmEmailRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    /**
     * Handle account registration request
     *
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $partnerLogin = null;
        if(session('partner') && is_string(session('partner'))){
            if(User::where('login', session('partner'))->exists()){
                $partnerLogin = session('partner');
            }
        }
        UserInfo::create([
            'uid'   => $user->id,
            'email' => $request->email,
            'partner' => $partnerLogin
        ]);
        auth()->login($user);

        // Первичная отправка кода на почту

        $code = rand(11111,99999);

        session(['emailCode' => $code]);

        return response()->json([
            'from'      => 'register'
        ]);
    }

    public function registerConfirm(ConfirmEmailRequest $request){

        $user = User::where('id', '=', Auth::user()->id)->first();
        $user->markEmailAsVerified();

        return true;
    }
}
