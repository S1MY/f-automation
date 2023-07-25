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

    public function loginPage(){
        return view('mainPages.login');
    }

    public function consentPage(){
        return view('mainPages.consent');
    }

    public function registerPage(){
        return view('mainPages.register');
    }

    public function confirmPage(){
        return view('mainPages.confirm');
    }
}
