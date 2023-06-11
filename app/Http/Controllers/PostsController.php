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

use App\Models\Post;
use App\Models\PostSettings;
use App\Models\PostStory;

class PostsController extends Controller
{
    public function postCreate(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:20',
            'accType' => 'required|string|in:user,group',
            'accountId' => 'required|integer',
            'file.*' => 'required|file|mimetypes:video/avi,video/mp4,image/jpeg,image/png',
            'duration' => 'required|string',
            'message' => 'required|string|max:200'
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
            $groupSendId = $searchGroup->group_id;
        } else {
            $accountId = $validatedData['accountId'];
            $groupId = 0;
        }
        $searchAccount = VkAccount::where('id', '=', $accountId)->first();
        if (!$searchAccount) {
            return $this->generateJsonResponse('input', 'account', 'Аккаунт не найден');
        }
        $maxSeconds = 120;
        $postFiles = $validatedData['file'];
        $durationItems = explode("_;_", $string);
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
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка F3');
                }
                $photoContent = $photoGet->getBody()->getContents();
                $photoJSON = json_decode($photoContent);
                if (isset($photoJSON->error)) {
                    Storage::disk('public')->delete($fileLoadUrl . $fileName);
                    return $this->generateJsonResponse('error', 'Ошибка запроса', 'При отправке запроса на сервер ВК произошла ошибка F4');
                }
                $attachments = 'photo{'.$photoJSON->response[0]->owner_id.'}_{'.$photoJSON->response[0]->id.'}';
                $link = $photoJSON->response[0]->sizes[4]->url;
                $fileId = $photoJSON->response[0]->id;
            }else{
                $attachments = 'video{'.$sendPostJSON->owner_id.'}_{'.$sendPostJSON->video_id.'}';
                $link = $sendPostJSON->direct_link;
                $fileId = $sendPostJSON->video_id;
            }
            Storage::disk('public')->delete($fileLoadUrl . $fileName);
            $linksDB[] = $link;
            $filesDB[] = $fileId;
        }
        return $this->generateJsonResponse('success', 'Оно сработало', 'Да');
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
