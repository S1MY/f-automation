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
        $type = $request->type;
        if ($type == 'add' || $type == 'update') {
            $tokenGet = null;
            $usersGet = null;
            $code = explode("=", $request->link);
            $client = new Client([
                'base_uri' => 'https://oauth.vk.com',
            ]);
            $authClient = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
            $tokenParams = [
                'client_id' => '51661029',
                'client_secret' => 'jy1p89O8SPpXOMg2JwEe',
                'code' => $code[1],
                'redirect_uri' => 'https://api.vk.com/blank.html',
            ];
            $tokenGet = $client->request('GET', '/access_token', [
                'query' => $tokenParams
            ]);
            if ($tokenGet->getStatusCode() !== 200 || !$tokenGet->getBody()) {
                $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
            }
            $tokenContent = $tokenGet->getBody()->getContents();
            $tokenJSON = json_decode($tokenContent);
            if (isset($tokenJSON->error)) {
                $this->generateJsonResponse('error', 'Ошибка получения токена', 'При получении токена доступа произошла ошибка');
            }
            $token = $tokenJSON->access_token;
            $userId = $tokenJSON->user_id;
            $usersParams = [
                'v' => '5.131',
                'access_token' => $token,
                'user_ids' => $userId,
                'fields' => 'first_name, last_name, photo_200_orig'
            ];
            $usersGet = $authClient->request('GET', '/method/users.get', [
                'query' => $usersParams
            ]);
            if ($usersGet->getStatusCode() !== 200 || !$usersGet->getBody()) {
                $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
            }
            $usersContent = $usersGet->getBody()->getContents();
            $usersJSON = json_decode($usersContent);

            if (isset($usersJSON->error)) {
                $this->generateJsonResponse('error', 'Ошибка запроса', 'При получении данных пользователя возникла ошибка');
            } else {
                $userId = Auth::user()->id;
                $accountName = $usersJSON->response[0]->first_name . ' ' . $usersJSON->response[0]->last_name;
                $accountLink = 'https://vk.com/id' . $usersJSON->response[0]->id;
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
                    $this->generateJsonResponse('success', 'Всё прошло успешно', 'Аккаунт - <span>' . $accountName . '</span> - успешно привязан.<br> Страница автоматически перегрузится, чтобы изменения применились.');
                } else {
                    $searchAccount->uid = $userId;
                    $searchAccount->name = $accountName;
                    $searchAccount->link = $accountLink;
                    $searchAccount->avatar = $accountAvatar;
                    $searchAccount->token = $token;
                    $searchAccount->save();
                    if ($type == 'add') {
                        $searchStreams = Stream::where('account_id', '=', $searchAccount->id)->get();
                        foreach ($searchStreams as $stream) {
                            StreamComments::where('stream_id', '=', $stream->id)->delete();
                            StreamStory::where('stream_id', '=', $stream->id)->delete();
                            StreamSettings::where('stream_id', '=', $stream->id)->delete();
                            Storage::delete($stream->link);
                            Stream::where('id', '=', $stream->id)->delete();
                            VkGroup::where('account_id', '=', $searchAccount->id)->delete();
                        }
                        $this->generateJsonResponse('success', 'Аккаунт обновлён', 'Аккаунт - <span>' . $accountName . '</span> - обновлён.<br> Вся информация очищена.');
                    }
                    $this->generateJsonResponse('success', 'Аккаунт обновлён', 'Аккаунт - <span>' . $accountName . '</span> - обновлён.');
                }
            }
        } else {
            $this->generateJsonResponse('error', 'Неизвестный тип', 'Не пытайтесь лезть в код элемента ;)');
        }
    }
    public function vkUserRemove(Request $request)
    {
        $userId = Auth::user()->id;
        $accountId = $request->accountId;
        if (is_numeric($accountId)) {
            $searchAccount = VkAccount::where('id', '=', $accountId)
                ->where('uid', '=', $userId)
                ->first();
            if ($searchAccount) {
                $searchStreams = Stream::where('account_id', '=', $searchAccount->id)->get();
                foreach ($searchStreams as $stream) {
                    StreamComments::where('stream_id', '=', $stream->id)->delete();
                    StreamStory::where('stream_id', '=', $stream->id)->delete();
                    StreamSettings::where('stream_id', '=', $stream->id)->delete();
                    Storage::delete($stream->link);
                    Stream::where('id', '=', $stream->id)->delete();
                }
                VkAccount::where('id', '=', $accountId)->delete();
                $this->generateJsonResponse('success', 'Всё прошло успешно', 'Аккаунт был удалён.<br> Страница автоматически перегрузится, чтобы изменения применились.');
            } else {
                $this->generateJsonResponse('error', 'Ошибка удаления', 'Данный аккаунт не был найден, либо вы не являетесь его владельцем');
            }
        } else {
            $this->generateJsonResponse('error', 'Ошибка удаления', 'Неопределённое поле accountId');
        }
    }
    public function vkGroupList(Request $request)
    {
        $groupsGet = null;
        $groupsResponse = [];
        $userId = Auth::user()->id;
        $accountId = $request->accountId;
        if (is_numeric($accountId)) {
            $searchAccount = VkAccount::where('id', '=', $accountId)
                ->where('uid', '=', $userId)
                ->first();
            if ($searchAccount) {
                $authClient = new Client([
                    'base_uri' => 'https://api.vk.com',
                ]);
                $groupsParams = [
                    'v' => '5.131',
                    'access_token' => $searchAccount->token,
                    'extended' => 1,
                    'filter' => 'admin',
                    'fields' => 'id,name,photo_100',
                ];
                $groupsGet = $authClient->request('GET', '/method/groups.get', [
                    'query' => $groupsParams
                ]);
                if ($groupsGet->getStatusCode() !== 200 || !$groupsGet->getBody()) {
                    $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
                }
                $groupsContent = $groupsGet->getBody()->getContents();
                $groupsJSON = json_decode($groupsContent);
                if (isset($groupsJSON->error)) {
                    $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
                }
                foreach ($groupsJSON->response->items as $groupItem) {
                    $searchGroup = VkGroup::where('group_id', '=', $groupItem->id)->first();
                    if (!$searchGroup) {
                        $groupsResponse[$groupItem->screen_name] = [
                            'id' => $groupItem->id,
                            'name' => $groupItem->name,
                            'avatar' => $groupItem->photo_100,
                            'screen_name' => $groupItem->screen_name,
                        ];
                    }
                }
                $this->generateJsonResponse('success', 'Список подгружен', $groupsResponse);
            } else {
                $this->generateJsonResponse('error', 'Ошибка запроса', 'Данный аккаунт не был найден, либо вы не являетесь его владельцем');
            }
        } else {
            $this->generateJsonResponse('error', 'Ошибка запроса', 'Неопределённое поле accountId');
        }
    }
    public function vkGroupAuth(Request $request)
    {
        $groupsGet = null;
        $userId = Auth::user()->id;
        $accountId = $request->accountId;
        $groupId = $request->groupId;
        if (is_numeric($accountId) && is_numeric($groupId)) {
            $searchAccount = VkAccount::where('id', '=', $accountId)
                ->where('uid', '=', $userId)
                ->first();
            if ($searchAccount) {
                $authClient = new Client([
                    'base_uri' => 'https://api.vk.com',
                ]);
                $groupsParams = [
                    'v' => '5.131',
                    'access_token' => $searchAccount->token,
                    'extended' => 1,
                    'filter' => 'admin',
                    'fields' => 'id,name,photo_100',
                ];
                $groupsGet = $authClient->request('GET', '/method/groups.get', [
                    'query' => $groupsParams
                ]);
                if ($groupsGet->getStatusCode() !== 200 || !$groupsGet->getBody()) {
                    $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
                }
                $groupsContent = $groupsGet->getBody()->getContents();
                $groupsJSON = json_decode($groupsContent);
                if (isset($groupsJSON->error)) {
                    $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
                }
                $searchGroup = VkGroup::where('group_id', '=', $groupId)->first();
                if (!$searchGroup) {
                    $groupNext = false;
                    $groupName = '';
                    foreach ($groupsJSON->response->items as $groupItem) {
                        if ($groupItem->id == $groupId) {
                            VkGroup::create([
                                'uid' => $userId,
                                'account_id' => $accountId,
                                'group_id' => $groupItem->id,
                                'name' => $groupItem->name,
                                'avatar' => $groupItem->photo_100
                            ]);
                            $groupNext = true;
                            $groupName = $groupItem->name;
                        }
                    }
                    if ($groupNext) {
                        $this->generateJsonResponse('success', 'Всё прошло успешно', 'Группа - <span>' . $groupName . '</span> - успешно привязана.<br> Страница автоматически перегрузится, чтобы изменения применились.');
                    } else {
                        $this->generateJsonResponse('error', 'Ошибка запроса', 'Вы не являетесь администратором группы!');
                    }
                } else {
                    $this->generateJsonResponse('error', 'Ошибка запроса', 'Данная группа уже есть в базе данных');
                }
            } else {
                $this->generateJsonResponse('error', 'Ошибка запроса', 'Данный аккаунт не был найден, либо вы не являетесь его владельцем');
            }
        } else {
            $this->generateJsonResponse('error', 'Ошибка запроса', 'Неопределённое поле accountId или groupId');
        }
    }
    public function vkGroupRemove(Request $request)
    {
        $userId = Auth::user()->id;
        $groupId = $request->groupId;
        if (is_numeric($groupId)) {
            $searchGroup = VkGroup::where('id', '=', $groupId)
                ->where('uid', '=', $userId)
                ->first();
            if ($searchGroup) {
                $searchStreams = Stream::where('group_id', '=', $searchGroup->id)->get();
                foreach ($searchStreams as $stream) {
                    StreamComments::where('stream_id', '=', $stream->id)->delete();
                    StreamStory::where('stream_id', '=', $stream->id)->delete();
                    StreamSettings::where('stream_id', '=', $stream->id)->delete();
                    Storage::delete($stream->link);
                    Stream::where('id', '=', $stream->id)->delete();
                }
                VkGroup::where('id', '=', $groupId)->delete();
                $this->generateJsonResponse('success', 'Всё прошло успешно', 'Группа была удалена.<br> Страница автоматически перегрузится, чтобы изменения применились.');
            } else {
                $this->generateJsonResponse('error', 'Ошибка удаления', 'Данная группа не была найдена, либо вы не являетесь её владельцем');
            }
        } else {
            $this->generateJsonResponse('error', 'Ошибка удаления', 'Неопределённое поле groupId');
        }
    }
    private function generateJsonResponse($type, $title, $message)
    {
        return response()->json([
            'status' => $type,
            'title' => $title,
            'message' => $message
        ]);
    }
}