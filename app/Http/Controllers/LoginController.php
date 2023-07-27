<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendCode;

class LoginController extends Controller
{

    public function login(LoginRequest $request)
    {
        $credentials = $request->getCredentials();

        if(!Auth::validate($credentials)):
            return response()->json([
                'errors' => [
                    'not found' => trans('auth.failed')
                ]
            ], 422);
        endif;

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        Auth::login($user);

        if( Auth::user()->email_verified_at == null ){
            $code = rand(11111,99999);
            session(['emailCode' => $code]);
            session(['email' => Auth::user()->UserInfo->email]);
            Mail::to(Auth::user()->UserInfo->email)->send(new SendCode($code));
        }

        return response()->json([
            'from'      => 'login'
        ]);
    }
}
