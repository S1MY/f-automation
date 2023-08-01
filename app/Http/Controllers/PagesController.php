<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function homePage(){
        return view('mainPages.home');
    }

    public function privacy(){
        return view('mainPages.privacy');
    }
    
    public function agreement(){
        return view('mainPages.agreement');
    }

    public function loginPage(){
        return view('mainPages.login');
    }

    public function consentPage(){
        return view('mainPages.consent');
    }

    public function registerPage(){
        return view('mainPages.register');
    }

    public function repairPage(){
        return view('mainPages.repair');
    }

    public function confirmPage(){
        return view('mainPages.confirm');
    }
    public function paymentError(){
        return view('mainPages.payment-error');
    }
    public function paymentSuccess(){
        return view('mainPages.payment-success');
    }
}
