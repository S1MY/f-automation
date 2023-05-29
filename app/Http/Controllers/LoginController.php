<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

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

        // Если аккаунт не подтверждён и ещё не удалён
        if( Auth::user()->email_verified_at == null ){
            $code = rand(11111,99999);

            session(['emailCode' => $code]);
        }

        return response()->json([
            'from'      => 'login'
        ]);
    }
}
