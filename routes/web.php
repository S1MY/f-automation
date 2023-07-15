<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CabinetPagesController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PaginateController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SubscribeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CronController;
use App\Http\Controllers\TgCronController;

use App\Http\Controllers\VkAuthController;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\StreamsController;

use App\Http\Controllers\TgAuthController;
use App\Http\Controllers\TelegramController;

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
        Route::prefix('/streams')->group(function () {
            Route::get('/', [CabinetPagesController::class, 'streams'])->name('streams');
            Route::get('/item{myPageId}', [CabinetPagesController::class, 'streamInfo'])->name('streamInfo');
            Route::get('/item{myPageId}/comments{myCommentId}', [CabinetPagesController::class, 'streamComments'])->name('streamComments');
        });
        Route::prefix('/posts')->group(function () {
            Route::get('/', [CabinetPagesController::class, 'posts'])->name('posts');
            Route::get('/item{myPageId}', [CabinetPagesController::class, 'postsInfo'])->name('postsInfo');
        });
        Route::prefix('/stories')->group(function () {
            Route::get('/', [CabinetPagesController::class, 'stories'])->name('stories');
            Route::get('/item{myPageId}', [CabinetPagesController::class, 'storiesInfo'])->name('storiesInfo');
        });
    });

    Route::group(['prefix' => 'tg'], function(){
        Route::get('/tg_streams', [CabinetPagesController::class, 'tg_streams'])->name('tg_streams');
        Route::get('/tg_posts', [CabinetPagesController::class, 'tg_posts'])->name('tg_posts');
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

Route::post('/password-change', [SettingsController::class, 'changePassword'])->name('changePassword');
Route::post('/newsletter-change', [SettingsController::class, 'changeNewsletter'])->name('changeNewsletter');

Route::post('/subscribe', [SubscribeController::class, 'subscribe'])->name('doSubscribe');
Route::post('/buy-account-place', [SubscribeController::class, 'buyAccountPlace'])->name('buyAccountPlace');
Route::post('/buy-group-place', [SubscribeController::class, 'buyGroupPlace'])->name('buyGroupPlace');

Route::post('/set-offers', [OfferController::class, 'setOffer'])->name('setOffer');
Route::post('/get-vote', [OfferController::class, 'getVote'])->name('getVote');

// Вконтакте
Route::post('/vk-user-auth', [VkAuthController::class, 'vkUserAuth'])->name('vkUserAuth');
Route::post('/vk-user-remove', [VkAuthController::class, 'vkUserRemove'])->name('vkUserRemove');
Route::post('/vk-group-list', [VkAuthController::class, 'vkGroupList'])->name('vkGroupList');
Route::post('/vk-group-auth', [VkAuthController::class, 'vkGroupAuth'])->name('vkGroupAuth');
Route::post('/vk-group-remove', [VkAuthController::class, 'vkGroupRemove'])->name('vkGroupRemove');

Route::post('/vk-stories-create', [StoriesController::class, 'storiesCreate'])->name('storiesCreate');
Route::post('/vk-stories-remove', [StoriesController::class, 'storiesRemove'])->name('storiesRemove');
Route::post('/vk-stories-activator', [StoriesController::class, 'storiesActivator'])->name('storiesActivator');

Route::post('/vk-post-create', [PostsController::class, 'postCreate'])->name('postCreate');
Route::post('/vk-post-remove', [PostsController::class, 'postRemove'])->name('postRemove');
Route::post('/vk-post-activator', [PostsController::class, 'postActivator'])->name('postActivator');

Route::post('/vk-stream-create', [StreamsController::class, 'streamCreate'])->name('streamCreate');
Route::post('/vk-stream-activator', [StreamsController::class, 'streamActivator'])->name('streamActivator');
Route::post('/vk-stream-remove', [StreamsController::class, 'streamRemove'])->name('streamRemove');

// Телеграм
Route::post('/tg-user-auth', [TgAuthController::class, 'tgUserAuth'])->name('tgUserAuth');
Route::post('/tg-user-remove', [TgAuthController::class, 'tgUserRemove'])->name('tgUserRemove');

Route::post('/tg-channel-list', [TelegramController::class, 'tgChannelList'])->name('tgChannelList');
Route::post('/tg-channel-auth', [TelegramController::class, 'tgChannelAuth'])->name('tgChannelAuth');
Route::post('/tg-channel-remove', [TelegramController::class, 'tgChannelRemove'])->name('tgChannelRemove');
Route::post('/tg-stream-create', [TelegramController::class, 'streamCreate'])->name('tgStreamCreate');
Route::post('/tg-stream-activator', [TelegramController::class, 'streamActivator'])->name('tgStreamActivator');
Route::post('/tg-stream-remove', [TelegramController::class, 'streamRemove'])->name('tgStreamRemove');

// Админские роуты
Route::post('/set-promo', [AdminController::class, 'setPromocodes'])->name('setPromo')->middleware('is_admin');
Route::post('/offer-result', [AdminController::class, 'offerUpdate'])->name('offerUpdate')->middleware('is_admin');

// Помогающие роуты
Route::post('/paginator', [PaginateController::class, 'paginate'])->name('paginate');

// Крутые роуты
Route::get('/upload-process', function () {
    return true;
});
Route::post('/stream-continue', [CronController::class, 'streamContinue'])->name('streamContinue');
Route::post('/tg-stream-continue', [TgCronController::class, 'tgStreamContinue'])->name('tgStreamContinue');