<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Auth;
use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stream;
use App\Models\StreamComments;
use App\Models\StreamSettings;
use App\Models\StreamStory;


class VkAuthController extends Controller
{
    public function vkUserAuth(Request $request)
    {
        $tokenGet = null;
        $usersGet = null;
        $error = false;
        $code = explode("=", $request->link);
        $client = new Client([
            'base_uri' => 'https://oauth.vk.com',
        ]);
        $authClient = new Client([
            'base_uri' => 'https://api.vk.com',
        ]);
        $tokenParams = [
            'client_id'     => '51661029',
            'client_secret' => 'jy1p89O8SPpXOMg2JwEe',
            'code'          => $code[1],
            'redirect_uri'  => 'https://api.vk.com/blank.html',
        ];
        $tokenGet = $client->request('GET', '/access_token', [
            'query' => $tokenParams
        ]);
        if ($tokenGet->getStatusCode() !== 200) {
            $error = true;
        }
        if ($error || !$tokenGet->getBody()) {
            return response()->json([
                'status' => 'error',
                'title' => 'Ошибка запроса',
                'message' => 'При отправке запроса на сервер ВК произошла ошибка'
                ]);
        }
        $tokenContent = $tokenGet->getBody()->getContents();
        $tokenJSON = json_decode($tokenContent);
        if (isset($tokenJSON->error)) {
            return response()->json([
                'status' => 'error',
                'title' => 'Ошибка получения токена',
                'message' => 'При получении токена доступа произошла ошибка'
                ]);
        }
        $token = $tokenJSON->access_token;
        $userId = $tokenJSON->user_id;
        $usersParams = [
            'v'            => '5.131',
            'access_token' => $token,
            'user_ids'     => $userId,
            'fields'       => 'first_name, last_name, photo_200_orig'
        ];
        $usersGet = $authClient->request('GET', '/method/users.get', [
            'query' => $usersParams
        ]);
        if ($usersGet->getStatusCode() !== 200) {
            $error = true;
        }
        if ($error || !$usersGet->getBody()) {
            return response()->json([
                'status' => 'error',
                'title' => 'Ошибка запроса',
                'message' => 'При отправке запроса на сервер ВК произошла ошибка'
                ]);
        }
        $usersContent = $usersGet->getBody()->getContents();
        $usersJSON = json_decode($usersContent);

        if (isset($usersJSON->error)) {
            return response()->json([
                'status' => 'error',
                'title' => 'Ошибка запроса',
                'message' => 'При получении данных пользователя возникла ошибка'
                ]);
        }else{
            $userId = Auth::user()->id;
            $accountName = $usersJSON->response[0]->first_name.' '.$usersJSON->response[0]->last_name;
            $accountLink = 'https://vk.com/id'.$usersJSON->response[0]->id;
            $accountAvatar = $usersJSON->response[0]->photo_200_orig;
            $searchAccount = VkAccount::where('link', '=', $accountLink)->first();
            if (!$searchAccount) {
                VkAccount::create([
                    'uid' => $userId,
                    'name' => $accountName,
                    'link' => $accountLink,
                    'avatar' => $accountAvatar,
                    'token' => $token
                ]); 
                return response()->json([
                    'status' => 'success',
                    'title' => 'Всё прошло успешно',
                    'message' => 'Аккаунт - <span>'. $accountName .'</span> - успешно привязан.<br> Страница автоматически перегрузится, чтобы изменения применились.'
                ]);
            }else{
                $searchAccount->uid = $userId;
                $searchAccount->save();

                $searchStreams = Stream::where('account_id', '=', $searchAccount->id)->get();
                foreach ($searchStreams as $stream) {
                    StreamComments::where('stream_id', '=', $stream->id)->delete();
                    StreamStory::where('stream_id', '=', $stream->id)->delete();
                    StreamSettings::where('stream_id', '=', $stream->id)->delete();
                    Storage::delete($stream->link);
                    Stream::where('id', '=', $stream->id)->delete();
                }
                return response()->json([
                    'status' => 'success',
                    'title' => 'Аккаунт перепривязан',
                    'message' => 'Аккаунт - <span>'. $accountName .'</span> - перепривязан.<br> Вся информация очищена.'
                ]);
            }
        }
    }
    public function vkUserRemove(Request $request){
        $userId = Auth::user()->id;
        $accountId = $request->accountId;
        if (is_numeric($accountId)) {
            $searchAccount = VkAccount::where('id', '=', $accountId)
                          ->where('uid', '=', $userId)
                          ->first();
            if($searchAccount){
                $searchStreams = Stream::where('account_id', '=', $searchAccount->id)->get();
                foreach ($searchStreams as $stream) {
                    StreamComments::where('stream_id', '=', $stream->id)->delete();
                    StreamStory::where('stream_id', '=', $stream->id)->delete();
                    StreamSettings::where('stream_id', '=', $stream->id)->delete();
                    Storage::delete($stream->link);
                    Stream::where('id', '=', $stream->id)->delete();
                }
                VkAccount::where('id', '=', $accountId)->delete();
                return response()->json([
                    'status' => 'success',
                    'title' => 'Всё прошло успешно',
                    'message' => 'Аккаунт был удалён.<br> Страница автоматически перегрузится, чтобы изменения применились.'
                ]);
            }else{
                return response()->json([
                    'status' => 'error',
                    'title' => 'Ошибка удаления',
                    'message' => 'Данный акаунт не был найден, либо вы не являетесь его владельцем'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'error',
                'title' => 'Ошибка удаления',
                'message' => 'Неопределённое поле accountId'
            ]);
        }
    }
    public function vkGroupList(Request $request)
    {
        $groupsGet = null;
        $groupsResponse = [];
        $error = false;
        $userId = Auth::user()->id;
        $accountId = $request->accountId;
        if (is_numeric($accountId)) {
            $searchAccount = VkAccount::where('id', '=', $accountId)
                          ->where('uid', '=', $userId)
                          ->first();
            if($searchAccount){
                $authClient = new Client([
                    'base_uri' => 'https://api.vk.com',
                ]);
                $groupsParams = array(
                    'v' => '5.131',
                    'access_token' => $token,
                    'extended' => 1,
                    'filter' => 'admin',
                    'fields' => 'id,name,photo_100',
                );
                $groupsGet = $authClient->request('GET', '/method/groups.get', [
                    'query' => $groupsParams
                ]);
                if ($groupsGet->getStatusCode() !== 200) {
                    $error = true;
                }
                if ($error || !$groupsGet->getBody()) {
                    return response()->json([
                        'status' => 'error',
                        'title' => 'Ошибка запроса',
                        'message' => 'При отправке запроса на сервер ВК произошла ошибка'
                    ]);
                }
                $groupsContent = $groupsGet->getBody()->getContents();
                $groupsJSON = json_decode($groupsContent);
                if (isset($groupsJSON->error)) {
                    return response()->json([
                        'status' => 'error',
                        'title' => 'Ошибка получения токена',
                        'message' => 'При получении групп возникла ошибка'
                    ]);
                }
                foreach ($groupsJSON->response->items as $groupItem) {
                    $searchGroup = VkGroup::where('group_id', '=', $groupItem->id)->first();
                    if(!$searchGroup){
                        $groupsResponse[$groupItem->screen_name] = [
                            'id' => $groupItem->id,
                            'name' => $groupItem->name,
                            'avatar' => $groupItem->photo_100,
                            'screen_name' => $groupItem->screen_name,
                        ];
                    }
                    return response()->json([
                        'status' => 'success',
                        'title' => 'Список подгружен',
                        'message' => $groupsResponse
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'error',
                    'title' => 'Ошибка удаления',
                    'message' => 'Данный акаунт не был найден, либо вы не являетесь его владельцем'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'error',
                'title' => 'Ошибка удаления',
                'message' => 'Неопределённое поле accountId'
            ]);
        }
    }
}
