<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmEmailRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\EmailRequest;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use App\Mail\SendCode;
use App\Mail\RegisterSuccess;
use App\Mail\RepairPassword;

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
        session(['email' => $request->email]);
        Mail::to($request->email)->send(new SendCode($code));
        return response()->json([
            'from'      => 'register'
        ]);
    }
    public function sendAgain(Request $request)
    {
        $code = rand(11111,99999);
        session(['emailCode' => $code]);
        Mail::to(session('email'))->send(new SendCode($code));
        return true;
    }

    public function registerConfirm(ConfirmEmailRequest $request)
    {
        $user = User::where('id', '=', Auth::user()->id)->first();
        $user->markEmailAsVerified();
        Mail::to($user->UserInfo->email)->send(new RegisterSuccess($user->login));
        return true;
    }

    public function repairPassword(EmailRequest $request)
    {
        $userInfo = UserInfo::where('email', '=', $request->email)->first();
        $user = User::where('id', '=', $userInfo->uid)->first();
        $password = $this->generatePassword();
        $user->password = Hash::make($password);
        $user->save();
        Mail::to($request->email)->send(new RepairPassword($password));
        return response()->json([
            'from' => 'register',
            'title' => 'Успех',
            'message' => 'Временный пароль был отправлен на почту!',
        ]);
    }

    private function generatePassword()
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $characterCount = strlen($characters);
    
        for ($i = 0; $i < 12; $i++) {
            $index = rand(0, $characterCount - 1);
            $password .= $characters[$index];
        }
    
        return $password;
    }
}
