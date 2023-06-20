<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use CURLFile;
use DateTime;

use Illuminate\Support\Facades\Auth;
use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Post;
use App\Models\PostSettings;
use App\Models\PostStory;

class PostsController extends Controller
{
    public function postCreate(Request $request)
    {
        $vkPostsRules = unserialize(Auth::user()->Subscribe->sub_config)['vk']['posts'];
        if($vkPostsRules['type1'] !== 1){
            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Создание постов недоступно для данного F-Аккаунта');
        }
        $rules = [
            'name' => 'required|string|max:20',
            'accType' => 'required|string|in:user,group',
            'accountId' => 'required|integer',
            'file.*' => 'required|file|mimetypes:video/avi,video/mp4,image/jpeg,image/png',
            'duration' => 'required|string',
            'message' => 'required|string|max:200',
            'closeComments' => 'integer|in:1,0',
            'onlyFriends' => 'integer|in:1,0',
            'removeOld' => 'integer|in:1,0',
            'pinNew' => 'integer|in:1,0',
            'type' => 'required|string|in:interval,date,daily',
            'interval' => 'integer|in:0,60,120,240,360,480',
            'daily' => 'integer|in:0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23',
            'date' => 'string'
        ];
        $messages = [
            'name.required' => 'Обязательное поле',
            'name.string' => 'Не является строкой',
            'name.max' => 'Максимум :max символов',
            'accType.required' => 'Неопределённый аккаунт',
            'accType.string' => 'Неопределённый аккаунт',
            'accType.in' => 'Неопределённый тип',
            'accountId.required' => 'Обязательное поле',
            'accountId.integer' => 'Не является числом',
            'file.required' => 'Файл является обязательным',
            'file.file' => 'Файл не является файлом',
            'file.mimetypes' => 'Доступны только avi, mp4, jpg, png',
            'duration.required' => 'Файл является обязательным',
            'duration.string' => 'Ошибка длительности файлов',
            'message.required' => 'Обязательное поле',
            'message.string' => 'Не является строкой',
            'message.max' => 'Максимум :max символов',
            'closeComments.integer' => 'Должно быть числом',
            'closeComments.in' => 'Должно быть 1 или 0',
            'onlyFriends.integer' => 'Должно быть числом',
            'onlyFriends.in' => 'Должно быть 1 или 0',
            'removeOld.integer' => 'Должно быть числом',
            'removeOld.in' => 'Должно быть 1 или 0',
            'pinNew.integer' => 'Должно быть числом',
            'pinNew.in' => 'Должно быть 1 или 0',
            'type.required' => 'Должно быть числом',
            'type.string' => 'Должно быть строкой',
            'type.in' => 'Неизвестный тип операции',
            'interval.integer' => 'Интервал должен быть числом',
            'interval.in' => 'Неизвестный тип интервала',
            'daily.integer' => 'Ежедневный выбор не число',
            'daily.in' => 'Должно быть числом от 0 до 23',
            'date.string' => 'Дата имеет некорректный формат',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('input', $firstErrorKey, $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();
        if ($validatedData['accType'] == 'group') {
            $groupId = $validatedData['accountId'];
            $searchGroup = VkGroup::where('id', '=', $groupId)->first();
            if (!$searchGroup) {
                return $this->generateJsonResponse('input', 'account', 'Группа не найдена');
            }
            $accountId = $searchGroup->account_id;
            $groupId = $searchGroup->group_id;
            if($vkPostsRules['type2'] !== 1){
                return $this->generateJsonResponse('error', 'Ошибка пакета', 'Создание постов в группах недоступно для данного F-Аккаунта');
            }
        } else {
            $accountId = $validatedData['accountId'];
            $groupId = 0;
        }
        $searchAccount = VkAccount::where('id', '=', $accountId)->first();
        $accountSendId = preg_replace('/[^0-9]/', '', $searchAccount->link);
        if (!$searchAccount) {
            return $this->generateJsonResponse('input', 'account', 'Аккаунт не найден');
        }
        if($validatedData['type'] === 'date'){
            if (DateTime::createFromFormat('d-m-Y H:i', $validatedData['date']) === false) {
                return $this->generateJsonResponse('input', 'tabs', 'Неверная дата');
            }
            if(DateTime::createFromFormat('d-m-Y H:i', $validatedData['date']) <= new DateTime()){
                return $this->generateJsonResponse('input', 'tabs', 'Дата и время меньше текущих');
            }
        }
        if($validatedData['type'] === 'interval'){
            if($validatedData['interval'] == 0){
                return $this->generateJsonResponse('input', 'tabs', 'Неверный интервал');
            }
        }
        if($validatedData['type'] === 'daily'){
            if($validatedData['daily'] == 0){
                return $this->generateJsonResponse('input', 'tabs', 'Неверное значение');
            }
        }
        // Сюда
        $maxSeconds = 120;
        $postFiles = $validatedData['file'];
        if(count($postFiles) > 1 && $vkPostsRules['type3'] !== 1){
            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Массив файлов для поста недоступен данного F-Аккаунта');
        }
        $durationItems = explode("_;_", $validatedData['duration']);
        foreach ($postFiles as $postFile) {
            $fileSize = $postFile->getSize();
            if ($fileSize > 419430400) {
                return $this->generateJsonResponse('input', 'file', 'Максимальный размер одного файла 50МБ');
            }
        }
        foreach ($durationItems as $durationItem) {
            if($durationItem > $maxSeconds){
                return $this->generateJsonResponse('input', 'file', 'Максимальная длительность видео'.$maxSeconds.' секунд');
            }
        }
        $linksDB = [];
        $filesDB = [];
        foreach ($postFiles as $postFile) {
            $fileType = $postFile->getMimeType();
            if ($fileType == 'video/mp4' || $fileType == 'video/avi') {
                $extension = 'mp4';
                $method = '/video.save';
                $sendType = 'video';
            } elseif ($fileType == 'image/jpeg' || $fileType == 'image/png') {
                $extension = 'jpg';
                $method = '/photos.getWallUploadServer';
                $sendType = 'photo';
            } else {
                return $this->generateJsonResponse('input', 'file', 'Неизвестный тип файла');
            }
            $fileName = 'post-' . date("Y-m-d-H-i-s") . "." . $extension;
            $userName = Auth::user()->login;
            Storage::disk('public')->putFileAs('files/users/' . $userName . '/posts', $postFile, $fileName);
            $getUpload = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
            $uploadParams = [
                'v' => '5.131',
                'access_token' => $searchAccount->token
            ];
            $uploadGet = $getUpload->request('GET', '/method' . $method, [
                'query' => $uploadParams
            ]);
            $fileLoadUrl = 'files/users/' . $userName . '/posts/';
            if ($uploadGet->getStatusCode() !== 200 || !$uploadGet->getBody()) {
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Ссылка для загрузки файла не получена F1');
            }
            $uploadContent = $uploadGet->getBody()->getContents();
            $uploadJSON = json_decode($uploadContent);
            if (isset($uploadJSON->error)) {
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Ссылка для загрузки файла не получена F2');
            }
            $fileSend = storage_path('app/public/' . $fileLoadUrl . $fileName);
            // Эта хуйня работает только с курлом ХЗ почему
            $sendPostInit = curl_init();
            if ($sendType == 'photo') {
                $postFields = [
                    'photo' => new CURLFile($fileSend)
                ];
            } else {
                $postFields = [
                    'video_file' => new CURLFile($fileSend)
                ];
            }
            curl_setopt_array($sendPostInit, [
                CURLOPT_URL => $uploadJSON->response->upload_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postFields,
            ]);
            $sendPostContent = curl_exec($sendPostInit);
            $sendPostJSON = json_decode($sendPostContent);
            curl_close($sendPostInit);
            $sendPostCode = curl_getinfo($sendPostInit, CURLINFO_HTTP_CODE);
            if ($sendPostCode != 200 || isset($sendPostJSON->error)) {
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Ошибка при загрузке файла на ВК сервер F1');
            }
            if ($sendType == 'photo') {
                $photoClient = new Client([
                    'base_uri' => 'https://api.vk.com',
                ]);
                $photoParams = [
                    'v' => '5.131',
                    'access_token' => $searchAccount->token,
                    'photo' => $sendPostJSON->photo,
                    'server' => $sendPostJSON->server,
                    'hash' => $sendPostJSON->hash,
                ];
                $photoGet = $photoClient->request('GET', '/method/photos.saveWallPhoto', [
                    'query' => $photoParams
                ]);
                if ($photoGet->getStatusCode() !== 200 || !$photoGet->getBody()) {
                    Storage::disk('public')->delete($fileLoadUrl . $fileName);
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось сохранить изображение F1');
                }
                $photoContent = $photoGet->getBody()->getContents();
                $photoJSON = json_decode($photoContent);
                if (isset($photoJSON->error)) {
                    Storage::disk('public')->delete($fileLoadUrl . $fileName);
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось сохранить изображение F2');
                }
                $attachments = 'photo'.$photoJSON->response[0]->owner_id.'_'.$photoJSON->response[0]->id;
                $link = $photoJSON->response[0]->sizes[3]->url;
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
            }else{
                $attachments = 'video'.$sendPostJSON->owner_id.'_'.$sendPostJSON->video_id;
                $link = $fileLoadUrl . $fileName;
            }
            $linksDB[] = $link;
            $filesDB[] = $attachments;
        }
        if($type === 'date'){
            $postDid = 0;
        }else{
            $wallPostSave = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
            $wallPostParams = [
                'v' => '5.131',
                'access_token' => $searchAccount->token,
                'friends_only' => $validatedData['onlyFriends'],
                'close_comments' => $validatedData['closeComments'],
                'message' => $validatedData['message'],
                'attachments' => implode(',',$filesDB),
            ];
            if ($validatedData['accType'] == 'group') {
                $wallPostParams['owner_id'] = '-'.$groupId;
                $wallPostParams['from_group'] = 1;
            }else{
                $wallPostParams['owner_id'] = $accountSendId;
            }
            $wallPostGet = $wallPostSave->request('GET', '/method/wall.post', [
                'query' => $wallPostParams
            ]);
            if ($wallPostGet->getStatusCode() !== 200 || !$wallPostGet->getBody()) {
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось создать пост F1');
            }
            $wallPostContent = $wallPostGet->getBody()->getContents();
            $wallPostJSON = json_decode($wallPostContent);
            if (isset($wallPostJSON->error)) {
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось создать пост F2', $wallPostParams);
            }
            if($validatedData['pinNew'] == 1){
                $postPinClient = new Client([
                    'base_uri' => 'https://api.vk.com',
                ]);
                $postPinParams = [
                    'v' => '5.131',
                    'access_token' => $searchAccount->token,
                    'post_id' => $wallPostJSON->response->post_id
                ];
                if ($validatedData['accType'] == 'group') {
                    $postPinParams['owner_id'] = '-'.$groupId;
                }else{
                    $postPinParams['owner_id'] = $accountSendId;
                }
                $postPinGet = $postPinClient->request('GET', '/method/wall.pin', [
                    'query' => $postPinParams
                ]);
                if ($postPinGet->getStatusCode() !== 200 || !$postPinGet->getBody()) {
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось закрепить пост на стене F1');
                }
                $postPinContent = $postPinGet->getBody()->getContents();
                $postPinJSON = json_decode($postPinContent);
                if (isset($postPinJSON->error)) {
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'Не удалось закрепить пост на стене F2');
                }
            }
            $postDid = $wallPostJSON->response->post_id;
        }
        $postCreate = Post::create([
            'account_id' => $accountId,
            'group_id' => $groupId,
            'title' => $validatedData['name'],
            'message' => $validatedData['message'],
            'link' => serialize($linksDB),
            'attachments' => serialize($filesDB),
            'active' => 1
        ]);
        $settingsData = [
            'post_id' => $postCreate->id,
            'friends_only' => $validatedData['onlyFriends'],
            'close_comments' => $validatedData['closeComments'],
            'pin_new' => $validatedData['pinNew'],
            'remove_old' => $validatedData['removeOld'],
        ];
        if ($type === 'date') {
            $settingsData['type'] = 'date';
            $timestamp = strtotime($validatedData['date']);
            $timestampDate = date('Y-m-d H:i:s', $timestamp);
            $settingsData['date'] = $timestampDate;
        } elseif ($type === 'interval') {
            $settingsData['type'] = 'interval';
            $settingsData['interval'] = $validatedData['interval'];
        }else{
            $settingsData['type'] = 'daily';
            $settingsData['daily'] = $validatedData['daily'];
        }
        PostSettings::create($settingsData);
        PostStory::create([
            'post_id' => $postCreate->id,
            'post_did' => $postDid
        ]);
        if ($validatedData['accType'] == 'group') {
            if($type !== 'date'){
                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Пост был загружен на стену группы');
            }else{
                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Пост будет загружен на стену группы в указанное время');
            }
        }else{
            if($type !== 'date'){
                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Пост был загружен на стену аккаунта');
            }else{
                return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Пост будет загружен на стену аккаунта в указанное время');
            }
        }
    }
    public function postRemove(Request $request)
    {
        $userId = Auth::user()->id;
        $removeId = $request->removeId;
        if (is_numeric($removeId)) {
            $searchPost = Post::where('id', '=', $removeId)
                ->first();
            $searchAccount = VkAccount::where('id', '=', $searchPost->account_id)->first();
            if($searchAccount){
                if($searchAccount->uid == $userId){
                    $ownerId = preg_replace('/[^0-9]/', '', $searchAccount->link);
                    if ($searchPost) {
                        if($searchPost->group_id > 0){
                            $ownerId = '-'.$searchPost->group_id;
                        }
                        PostSettings::where('post_id', '=', $removeId)->delete();
                        $attachments = unserialize($searchPost->attachments);
                        $links = unserialize($searchPost->link);
                        $searchPostStory = PostStory::where('post_id', '=', $removeId)->get();
                        foreach ($searchPostStory as $post) {
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
                        return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Пост удалён.<br> Страница автоматически перегрузится, чтобы изменения применились.');
                    }else{
                        return $this->generateJsonResponse('error', 'Ошибка удаления', 'Данный пост не найден');
                    }  
                }else{
                    return $this->generateJsonResponse('error', 'Ошибка удаления', 'Данный пост вам не принадлежит');
                } 
            }else{
                return $this->generateJsonResponse('error', 'Ошибка удаления', 'Не найден родительский аккаунт');
            }
        }else{
            return $this->generateJsonResponse('error', 'Ошибка удаления', 'Неопределённое поле removeId');
        }
    }
    public function postActivator(Request $request)
    {
        $userId = Auth::user()->id;
        $autoStart = $request->autoStart;
        if (is_numeric($autoStart)) {
            $searchPost = Post::where('id', '=', $autoStart)
                ->first();
            $postSettings = PostSettings::where('post_id', '=', $autoStart)
            ->first();
            $searchAccount = VkAccount::where('id', '=', $searchPost->account_id)->first();
            if($searchAccount){
                if($searchAccount->uid == $userId){
                    if ($searchPost) {
                        $newActive = ($searchPost->active == 1) ? 0 : 1;
                        $vkPostsRules = unserialize(Auth::user()->Subscribe->sub_config)['vk']['posts'];
                        if($newActive ==1 && $vkPostsRules['type1'] !== 1){
                            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Активация поста недоступна для данного F-Аккаунта');
                        }
                        if($searchPost->group_id > 0 && $vkPostsRules['type2'] !== 1){
                            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Активация поста в группе недоступна для данного F-Аккаунта');
                        }
                        if(count(unserialize($searchPost->attachments)) > 0 && $vkPostsRules['type3'] !== 1){
                            return $this->generateJsonResponse('error', 'Ошибка пакета', 'Массив файлов поста недоступен для данного F-Аккаунта');
                        }
                        $searchPost->active = $newActive;
                        $timestampDate = date('Y-m-d H:i:s', strtotime('+1 minute'));
                        if ($postSettings->type === 'date') {
                            $postSettings->date = $timestampDate;
                            $postSettings->save();
                        }
                        $searchPost->updated_at = $timestampDate;
                        $searchPost->save();
                        if($newActive == 1){
                            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Автозапуск включен. Пост будет выложен через минуту');
                        }else{
                            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Автозапуск выключен. Пост не будет выкладываться');
                        }
                    }else{
                        return $this->generateJsonResponse('error', 'Ошибка изменения', 'Данный пост не найден');
                    }
                }else{
                    return $this->generateJsonResponse('error', 'Ошибка изменения', 'Данный пост вам не принадлежит');
                }
            }else{
                return $this->generateJsonResponse('error', 'Ошибка изменения', 'Не найден родительский аккаунт');
            }
        }else{
            return $this->generateJsonResponse('error', 'Ошибка изменения', 'Неопределённое поле autoStart');
        }
    }
    private function generateJsonResponse($type, $title, $message, $errorJson = null)
    {
        return response()->json([
            'status' => $type,
            'title' => $title,
            'message' => $message,
            'errorJson' => $errorJson
        ]);
    }
}
