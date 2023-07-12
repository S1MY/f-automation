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

use App\Models\Stories;
use App\Models\StoriesSettings;
use App\Models\StoriesStory;

use App\Models\Post;
use App\Models\PostSettings;
use App\Models\PostStory;


class VkAuthController extends Controller
{
    public function vkUserAuth(Request $request)
    {
        $type = $request->type;
        if ($type == 'add' || $type == 'update') {
            $tokenGet = null;
            $usersGet = null;
            $code = $request->link;
            $code = substr($code, strpos($code, '#') + 1);
            parse_str($code, $params);
            $token = $params['access_token'];
            $userId = $params['user_id'];
            $authClient = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
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
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
            }
            $usersContent = $usersGet->getBody()->getContents();
            $usersJSON = json_decode($usersContent);
            if (isset($usersJSON->error)) {
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'При получении данных пользователя возникла ошибка');
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
                    return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Аккаунт - <span>' . $accountName . '</span> - успешно привязан.<br> Страница автоматически перегрузится, чтобы изменения применились.');
                } else {
                    $searchAccount->uid = $userId;
                    $searchAccount->name = $accountName;
                    $searchAccount->link = $accountLink;
                    $searchAccount->avatar = $accountAvatar;
                    $searchAccount->token = $token;
                    $searchAccount->save();
                    if ($type == 'add') {
                        $searchStreams = Stream::where('account_id', '=', $searchAccount->id)->get();
                        $this->removeStreams($searchStreams, $searchAccount);
                        $searchStories = Stories::where('account_id', '=', $searchAccount->id)->get();
                        $this->removeStorites($searchStories, $searchAccount);
                        $searchPosts = Post::where('account_id', '=', $searchAccount->id)->get();
                        $this->removePosts($searchPosts, $searchAccount);
                        VkGroup::where('account_id', '=', $searchAccount->id)->delete();
                        return $this->generateJsonResponse('success', 'Аккаунт обновлён', 'Аккаунт - <span>' . $accountName . '</span> - обновлён.<br> Вся информация очищена.');
                    }
                    return $this->generateJsonResponse('success', 'Аккаунт обновлён', 'Аккаунт - <span>' . $accountName . '</span> - обновлён.');
                }
            }
        } else {
            return $this->generateJsonResponse('error', 'Неизвестный тип', 'Не пытайтесь лезть в код элемента ;)');
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
                $this->removeStreams($searchStreams, $searchAccount);
                $searchStories = Stories::where('account_id', '=', $searchAccount->id)->get();
                $this->removeStorites($searchStories, $searchAccount);
                $searchPosts = Post::where('account_id', '=', $searchAccount->id)->get();
                $this->removePosts($searchPosts, $searchAccount);
                VkGroup::where('account_id', '=', $searchAccount->id)->delete();
                VkAccount::where('id', '=', $accountId)->delete();
                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Аккаунт был удалён.<br> Страница автоматически перегрузится, чтобы изменения применились.');
            } else {
                return $this->generateJsonResponse('error', 'Ошибка удаления', 'Данный аккаунт не был найден, либо вы не являетесь его владельцем');
            }
        } else {
            return $this->generateJsonResponse('error', 'Ошибка удаления', 'Неопределённое поле accountId');
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
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
                }
                $groupsContent = $groupsGet->getBody()->getContents();
                $groupsJSON = json_decode($groupsContent);
                if (isset($groupsJSON->error)) {
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
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
                return $this->generateJsonResponse('success', 'Список подгружен', $groupsResponse);
            } else {
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Данный аккаунт не был найден, либо вы не являетесь его владельцем');
            }
        } else {
            return $this->generateJsonResponse('error', 'Ошибка запроса', 'Неопределённое поле accountId');
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
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
                }
                $groupsContent = $groupsGet->getBody()->getContents();
                $groupsJSON = json_decode($groupsContent);
                if (isset($groupsJSON->error)) {
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка');
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
                        return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Группа - <span>' . $groupName . '</span> - успешно привязана.<br> Страница автоматически перегрузится, чтобы изменения применились.');
                    } else {
                        return $this->generateJsonResponse('error', 'Ошибка запроса', 'Вы не являетесь администратором группы!');
                    }
                } else {
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'Данная группа уже есть в базе данных');
                }
            } else {
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Данный аккаунт не был найден, либо вы не являетесь его владельцем');
            }
        } else {
            return $this->generateJsonResponse('error', 'Ошибка запроса', 'Неопределённое поле accountId или groupId');
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
            $searchAccount = VkAccount::where('id', '=', $searchGroup->account_id)->first();
            if ($searchGroup) {
                $searchStreams = Stream::where('group_id', '=', $searchGroup->id)->get();
                $this->removeStreams($searchStreams, $searchAccount);
                $searchStories = Stories::where('group_id', '=', $searchGroup->id)->get();
                $this->removeStorites($searchStories, $searchAccount);
                $searchPosts = Post::where('group_id', '=', $searchGroup->id)->get();
                $this->removePosts($searchPosts, $searchAccount);
                VkGroup::where('id', '=', $groupId)->delete();
                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Группа была удалена.<br> Страница автоматически перегрузится, чтобы изменения применились.');
            } else {
                return $this->generateJsonResponse('error', 'Ошибка удаления', 'Данная группа не была найдена, либо вы не являетесь её владельцем');
            }
        } else {
            return $this->generateJsonResponse('error', 'Ошибка удаления', 'Неопределённое поле groupId');
        }
    }
    protected function removeStreams($searchStreams, $searchAccount)
    {
        foreach ($searchStreams as $stream) {
            $videoGetClient = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
            $videoGetParams = [
                'v' => '5.131',
                'access_token' => $searchAccount->token
            ];
            if($stream->group_id > 0){
                $videoGetParams['owner_id'] = '-'.$stream->group_id;
            }else{
                $videoGetParams['owner_id'] = preg_replace('/[^0-9]/', '', $searchAccount->link);
            }
            $videoGetGet = $videoGetClient->request('GET', '/method/video.get', [
                'query' => $videoGetParams
            ]);
            if ($videoGetGet->getStatusCode() !== 200 || !$videoGetGet->getBody()) {
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось найти старые трансляции F1');
            }
            $videoGetContent = $videoGetGet->getBody()->getContents();
            $videoGetJSON = json_decode($videoGetContent);
            if (isset($videoGetJSON->error)) {
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось найти старые трансляции F2');
            }
            if($videoGetJSON->response->count > 0){
                foreach ($videoGetJSON->response->items as $key => $item) {
                    if($item->title == $searchStream->title){
                        $videoRemoveClient = new Client([
                            'base_uri' => 'https://api.vk.com',
                        ]);
                        $videoRemoveParams = [
                            'v' => '5.131',
                            'access_token' => $searchAccount->token,
                            'owner_id' => $item->owner_id,
                            'video_id' => $item->id,
                            'target_id' => $item->owner_id
                        ];
                
                        $videoRemoveGet = $videoRemoveClient->request('GET', '/method/video.delete', [
                            'query' => $videoRemoveParams
                        ]);
                
                        if ($videoRemoveGet->getStatusCode() !== 200 || !$videoRemoveGet->getBody()) {
                            return;
                        }
                        $videoRemoveContent = $videoRemoveGet->getBody()->getContents();
                        $videoRemoveJSON = json_decode($videoRemoveContent);
                        if (isset($videoRemoveJSON->error)) {
                            return;
                        }
                    }
                }
            }
            StreamComments::where('stream_id', '=', $stream->id)->delete();
            StreamStory::where('stream_id', '=', $stream->id)->delete();
            StreamSettings::where('stream_id', '=', $stream->id)->delete();
            foreach (unserialize($stream->link) as $key => $link) {
                Storage::delete($link);
            }
            Stream::where('id', '=', $stream->id)->delete();
        }
    }
    protected function removeStorites($searchStories, $searchAccount)
    {
        foreach ($searchStories as $stories) {
            StoriesStory::where('stories_id', '=', $stories->id)->delete();
            StoriesSettings::where('stories_id', '=', $stories->id)->delete();
            $storiesFiles = unserialize($stories->link);
            foreach ($storiesFiles as $storiesFile) {
                Storage::delete($storiesFile);
            }
            Stories::where('id', '=', $stories->id)->delete();
        }
    }
    protected function removePosts($searchPosts, $searchAccount)
    {
        foreach ($searchPosts as $post) {
            $ownerId = preg_replace('/[^0-9]/', '', $searchAccount->link);
            if($post->group_id > 0){
                $ownerId = '-'.$post->group_id;
            }
            PostSettings::where('post_id', '=', $post->id)->delete();
            $attachments = unserialize($post->attachments);
            $links = unserialize($post->link);
            $searchPostStory = PostStory::where('post_id', '=', $post->id)->get();
            foreach ($searchPostStory as $post2) {
                $postRemoveClient = new Client([
                    'base_uri' => 'https://api.vk.com',
                ]);
                $postRemoveParams = [
                    'v' => '5.131',
                    'access_token' => $searchAccount->token,
                    'owner_id' => $ownerId,
                    'post_id' => $searchPostStory->post_did
                ];
        
                $postRemoveGet = $postRemoveClient->request('GET', '/method/wall.delete', [
                    'query' => $postRemoveParams
                ]);
        
                if ($postRemoveGet->getStatusCode() !== 200 || !$postRemoveGet->getBody()) {
                    return;
                }
                $postRemoveContent = $postRemoveGet->getBody()->getContents();
                $postRemoveJSON = json_decode($postRemoveContent);
                if (isset($postRemoveJSON->error)) {
                    return;
                }
            }
            foreach ($links as $key => $link) {
                $itemId = explode("_", $attachments[$key])[1];
                if((rtrim(strstr($attachments[$key], '_', true), "0123456789") == 'video')){
                    Storage::delete($link);
                    $videoRemoveClient = new Client([
                        'base_uri' => 'https://api.vk.com',
                    ]);
                    $videoRemoveParams = [
                        'v' => '5.131',
                        'access_token' => $searchAccount->token,
                        'owner_id' => preg_replace('/[^0-9]/', '', $searchAccount->link),
                        'video_id' => $itemId,
                        'target_id' => preg_replace('/[^0-9]/', '', $searchAccount->link)
                    ];
            
                    $videoRemoveGet = $videoRemoveClient->request('GET', '/method/video.delete', [
                        'query' => $videoRemoveParams
                    ]);
            
                    if ($videoRemoveGet->getStatusCode() !== 200 || !$videoRemoveGet->getBody()) {
                        return;
                    }
                    $videoRemoveContent = $videoRemoveGet->getBody()->getContents();
                    $videoRemoveJSON = json_decode($videoRemoveContent);
                    if (isset($videoRemoveJSON->error)) {
                        return;
                    }
                }else{
                    $photoRemoveClient = new Client([
                        'base_uri' => 'https://api.vk.com',
                    ]);
                    $photoRemoveParams = [
                        'v' => '5.131',
                        'access_token' => $searchAccount->token,
                        'owner_id' => preg_replace('/[^0-9]/', '', $searchAccount->link),
                        'photo_id' => $itemId
                    ];
            
                    $photoRemoveGet = $photoRemoveClient->request('GET', '/method/photos.delete', [
                        'query' => $photoRemoveParams
                    ]);
            
                    if ($photoRemoveGet->getStatusCode() !== 200 || !$photoRemoveGet->getBody()) {
                        return;
                    }
                    $photoRemoveContent = $photoRemoveGet->getBody()->getContents();
                    $photoRemoveJSON = json_decode($photoRemoveContent);
                    if (isset($photoRemoveJSON->error)) {
                        return;
                    }
                }
            }
        }
    }
}