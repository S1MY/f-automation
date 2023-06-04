<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use CURLFile;

use Illuminate\Support\Facades\Auth;
use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;
use App\Models\StoriesSettings;
use App\Models\StoriesStory;

class StoriesController extends Controller
{
    public function storiesCreate(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:20',
            'accType' => 'required|string|in:user,group',
            'accountId' => 'required|integer',
            'file' => 'required|file|mimetypes:video/avi,video/mp4,image/jpeg,image/png',
            'duration' => 'required|numeric|regex:/^\d+(\.\d{1,8})?$/',
            'linkText' => 'string|in:learn_more,to_store,more,vote,book,order,enroll,fill,signup,buy,ticket,write,open,view,go_to,contact,watch,play,install,read',
            'linkUrl' => 'sometimes|nullable|url',
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
            'duration.numeric' => 'Длительность не numeric',
            'duration.regex' => 'Ошибка длительности видео',
            'linkText.string' => 'Не является строкой',
            'linkText.in' => 'Неопределённый тип кнопки',
            'linkUrl.url' => 'Не является ссылкой'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('input', $firstErrorKey, $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();

        $linkText = (isset($validatedData['linkText'])) ? $validatedData['linkText'] : 'learn_more';
        $linkUrl = (isset($validatedData['linkUrl'])) ? $validatedData['linkUrl'] : 'https://f-automation.v-devo.ru/';
        if ($validatedData['accType'] == 'group') {
            $groupId = $validatedData['accountId'];
            $searchGroup = VkGroup::where('id', '=', $groupId)->first();
            if (!$searchGroup) {
                return $this->generateJsonResponse('input', 'account', 'Группа не найдена');
            }
            $accountId = $searchGroup->account_id;
            $groupSendId = $searchGroup->group_id;
        } else {
            $accountId = $validatedData['accountId'];
            $groupId = 0;
        }
        $searchAccount = VkAccount::where('id', '=', $accountId)->first();
        if (!$searchAccount) {
            return $this->generateJsonResponse('input', 'account', 'Аккаунт не найден');
        }
        // Сколько секунд может загрузить чел
        // Надо будет делать функцию нарезки длинного видоса и отписание поочерёдной загрузки (ебано)
        $maxSeconds = 15;
        if($validatedData['duration'] > $maxSeconds){
            return $this->generateJsonResponse('input', 'file', 'Максимальная длительность '.$maxSeconds.' секунд');
        }
        $storiesFile = $validatedData['file'];
        $fileSize = $storiesFile->getSize();
        $fileType = $storiesFile->getMimeType();
        if ($fileSize <= 83886080) {
            if ($fileType == 'video/mp4' || $fileType == 'video/avi') {
                $extension = 'mp4';
                $method = '/stories.getVideoUploadServer';
                $sendType = 'video_file';
            } elseif ($fileType == 'image/jpeg' || $fileType == 'image/png') {
                $extension = 'jpg';
                $method = '/stories.getPhotoUploadServer';
                $sendType = 'file';
            } else {
                return $this->generateJsonResponse('input', 'file', 'Неизвестный тип файла');
            }
            $fileName = 'stories-' . date("Y-m-d-H-i-s") . "." . $extension;
            $userName = Auth::user()->login;
            Storage::disk('public')->putFileAs('files/users/' . $userName . '/stories', $storiesFile, $fileName);
            $getUpload = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
            $uploadParams = [
                'v' => '5.131',
                'access_token' => $searchAccount->token,
                'add_to_news' => 1
            ];
            if ($validatedData['accType'] == 'group') {
                $uploadParams['group_id'] = $groupSendId;
                $uploadParams['link_text'] = $linkText;
                $uploadParams['link_url'] = $linkUrl;
            }
            $uploadGet = $getUpload->request('GET', '/method' . $method, [
                'query' => $uploadParams
            ]);
            $fileLoadUrl = 'files/users/' . $userName . '/stories/';
            if ($uploadGet->getStatusCode() !== 200 || !$uploadGet->getBody()) {
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка F1');
            }
            $uploadContent = $uploadGet->getBody()->getContents();
            $uploadJSON = json_decode($uploadContent);
            if (isset($uploadJSON->error)) {
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка F2');
            }
            $fileSend = storage_path('app/public/' . $fileLoadUrl . $fileName);
            // Эта хуйня работает только с курлом ХЗ почему
            $sendStoriesPost = curl_init();
            if ($sendType == 'file') {
                $postFields = [
                    'file' => new CURLFile($fileSend)
                ];
            } else {
                $postFields = [
                    'video_file' => new CURLFile($fileSend)
                ];
            }
            curl_setopt_array($sendStoriesPost, [
                CURLOPT_URL => $uploadJSON->response->upload_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postFields,
            ]);
            $sendStoriesContent = curl_exec($sendStoriesPost);
            $sendStoriesJSON = json_decode($sendStoriesContent);
            curl_close($sendStoriesPost);
            $sendStoriesCode = curl_getinfo($sendStoriesPost, CURLINFO_HTTP_CODE);
            if ($sendStoriesCode != 200 || isset($sendStoriesJSON->error)) {
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'Ошибка при загрузке файла на ВК сервер F1');
            }
            $storiesSave = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
            $storiesParams = [
                'v' => '5.131',
                'access_token' => $searchAccount->token,
                'upload_results' => $sendStoriesJSON->response->upload_result,
            ];
            $storiesGet = $storiesSave->request('GET', '/method/stories.save', [
                'query' => $storiesParams
            ]);
            if ($storiesGet->getStatusCode() !== 200 || !$storiesGet->getBody()) {
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка F3');
            }
            $storiesContent = $storiesGet->getBody()->getContents();
            $storiesJSON = json_decode($storiesContent);
            if (isset($storiesJSON->error)) {
                Storage::disk('public')->delete($fileLoadUrl . $fileName);
                return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка F4');
            }
            $storiesCreate = Stories::create([
                'account_id' => $accountId,
                'group_id' => $groupId,
                'title' => $validatedData['name'],
                'filetype' => $extension,
                'link' => $fileLoadUrl . $fileName,
                'upload_result' => $sendStoriesJSON->response->upload_result,
                'active' => 0
            ]);

            StoriesSettings::create([
                'stories_id' => $storiesCreate->id,
                'link_text' => $linkText,
                'link_url' => $linkUrl,
            ]);
            StoriesStory::create([
                'stories_id' => $storiesCreate->id,
                'stories_did' => $storiesJSON->response->items[0]->id,
                'viewes' => 0,
                'open_link' => 0,
                'subscribers' => 0,
            ]);
            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'История успешно запущена в ВК');
        } else {
            return $this->generateJsonResponse('input', 'file', 'Максимальный размер файла 10МБ');
        }
    }

    public function storiesRemove(Request $request)
    {
        $userId = Auth::user()->id;
        $removeId = $request->autoStart;
        if (is_numeric($removeId)) {
            $searchStories = Stories::where('id', '=', $removeId)
                ->first();
            $searchAccount = VkAccount::where('id', '=', $searchStories->account_id)->first();
            if($searchAccount){
                if($searchAccount->uid == $userId){
                    if ($searchStories) {
                        StoriesStory::where('stories_id', '=', $removeId)->delete();
                        StoriesSettings::where('stories_id', '=', $removeId)->delete();
                        Storage::delete($searchStories->link);
                        Stories::where('id', '=', $removeId)->delete();
                        return $this->generateJsonResponse('success', 'Всё прошло успешно', 'История была удалена.<br> Страница автоматически перегрузится, чтобы изменения применились.');
                    }else{
                        return $this->generateJsonResponse('error', 'Ошибка удаления', 'Данная история не найдена');
                    }  
                }else{
                    return $this->generateJsonResponse('error', 'Ошибка удаления', 'Данная история вам не принадлежит');
                } 
            }else{
                return $this->generateJsonResponse('error', 'Ошибка удаления', 'Не найден родительский аккаунт');
            }
        }else{
            return $this->generateJsonResponse('error', 'Ошибка удаления', 'Неопределённое поле storiesId');
        }
    }
    public function storiesActivator(Request $request)
    {
        $userId = Auth::user()->id;
        $autoStart = $request->autoStart;
        if (is_numeric($autoStart)) {
            $searchStories = Stories::where('id', '=', $autoStart)
                ->first();
            $searchAccount = VkAccount::where('id', '=', $searchStories->account_id)->first();
            if($searchAccount){
                if($searchAccount->uid == $userId){
                    if ($searchStories) {
                        $newActive = ($searchStories->active == 1) ? 0 : 1;
                        $searchStories->active = $newActive;
                        $searchStories->save();
                        if($newActive == 1){
                            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Автозапуск включен. История запустится через минуту');
                        }else{
                            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Автозапуск выключен. История не будет запускаться');
                        }
                    }else{
                        return $this->generateJsonResponse('error', 'Ошибка изменения', 'Данная история не найдена');
                    }
                }else{
                    return $this->generateJsonResponse('error', 'Ошибка изменения', 'Данная история вам не принадлежит');
                }
            }else{
                return $this->generateJsonResponse('error', 'Ошибка изменения', 'Не найден родительский аккаунт');
            }
        }else{
            return $this->generateJsonResponse('error', 'Ошибка изменения', 'Неопределённое поле autoStart');
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