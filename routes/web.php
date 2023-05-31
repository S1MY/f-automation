<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CabinetPagesController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PaginateController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SubscribeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VkAuthController;

/*
 *
 * Главные страницы сайта
 *
 */

Route::get('/', [PagesController::class, 'homePage'])->name('home');
Route::get('/login', [PagesController::class, 'loginPage'])->name('login');
Route::get('/consent', [PagesController::class, 'consentPage'])->name('consent');
Route::get('/register', [PagesController::class, 'registerPage'])->name('register');
Route::get('/confirm', [PagesController::class, 'confirmPage'])->name('confirm')->middleware('auth');

/*
 *
 * Страницы кабинета
 *
 */

Route::group(['prefix' => 'cabinet', 'middleware' => ['auth', 'confirmed']], function(){
    Route::get('/actions', [CabinetPagesController::class, 'actions'])->name('actions');
    Route::get('/', [CabinetPagesController::class, 'cabinet'])->name('cabinet');
    Route::get('/subscribe', [CabinetPagesController::class, 'subscribe'])->name('subscribe');


    Route::group(['prefix' => 'vk'], function(){
        Route::get('/streams', [CabinetPagesController::class, 'streams'])->name('streams');
        Route::get('/stream-info', [CabinetPagesController::class, 'streamInfo'])->name('streamInfo');
        Route::get('/stream-comments', [CabinetPagesController::class, 'streamComments'])->name('streamComments');
        Route::get('/posts', [CabinetPagesController::class, 'posts'])->name('posts');
        Route::get('/stories', [CabinetPagesController::class, 'stories'])->name('stories');
        Route::get('/stories-info', [CabinetPagesController::class, 'storiesInfo'])->name('storiesInfo');
    });

    Route::group(['prefix' => 'telegram'], function(){

    });

    Route::get('/partners', [CabinetPagesController::class, 'partners'])->name('partners');
    Route::get('/guide', [CabinetPagesController::class, 'guide'])->name('guide');
    Route::get('/settings', [CabinetPagesController::class, 'settings'])->name('settings');
    Route::get('/offers', [CabinetPagesController::class, 'offers'])->name('offers');

    Route::get('/admin', [CabinetPagesController::class, 'admin'])->name('admin')->middleware('is_admin');
});

/*
 *
 * Запросы к сайту
 *
 */

Route::post('/user-register', [RegisterController::class, 'register'])->name('doregistration');
Route::post('/user-login', [LoginController::class, 'login'])->name('dologin');
Route::post('/user-confirm-email', [RegisterController::class, 'registerConfirm'])->name('registerConfirm');
Route::get('/logout', [LogoutController::class, 'perform'])->name('logout');

Route::post('/subscribe', [SubscribeController::class, 'subscribe'])->name('doSubscribe');

Route::post('/set-offers', [OfferController::class, 'setOffer'])->name('setOffer');
Route::post('/get-vote', [OfferController::class, 'getVote'])->name('getVotes');

// Вконтакте
Route::post('/vk-user-auth', [VkAuthController::class, 'vkUserAuth'])->name('vkUserAuth');
Route::post('/vk-user-remove', [VkAuthController::class, 'vkUserRemove'])->name('vkUserRemove');
Route::post('/vk-group-list', [VkAuthController::class, 'vkGroupList'])->name('vkGroupList');

// Админские роуты
Route::post('/set-promo', [AdminController::class, 'setPromocodes'])->name('setPromo')->middleware('is_admin');
Route::post('/offer-result', [AdminController::class, 'offerUpdate'])->name('offerUpdate')->middleware('is_admin');

// Помогающие роуты
Route::post('/paginator', [PaginateController::class, 'paginate'])->name('paginate');
