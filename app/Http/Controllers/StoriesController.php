<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;
use App\Models\VkAccount;
use App\Models\VkGroup;

use App\Models\Stories;

class StoriesController extends Controller
{
    public function storiesCreate(Request $request)
    {   
        $rules = [
            'name' => 'required|string|max:20',
            'accType' => 'required|string|in:user,group',
            'accountId' => 'required|integer',
            'file' => 'required|file|mimetypes:video/avi,video/mp4,image/jpeg,image/png',
            'duration' => 'required|integer'
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
            'duration.float' => 'Ошибка длительности видео',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return response()->json([
                'status' => 'input',
                'name' => $firstErrorKey,
                'message' => $errors[$firstErrorKey][0]
            ]);
        }
        $validatedData = $validator->validated();
        if($validatedData['accType'] == 'group'){
            $groupId = $validatedData['accountId'];
            $searchGroup = VkGroup::where('group_id', '=', $groupId)->first();
            if(!$searchGroup){
                return response()->json([
                    'status' => 'success',
                    'title' => 'Ошибка запроса',
                    'message' => 'Группа не найдена'
                ]);
            }
            $accountId = $searchGroup->account_id;
        }else{
            $accountId = $validatedData['accountId'];
            $groupId = 0;
        }
        $searchAccount = VkAccount::where('id', '=', $accountId)->first();
        if(!$searchAccount){
            return response()->json([
                'status' => 'success',
                'title' => 'Ошибка запроса',
                'message' => 'Аккаунт не найден'
            ]);
        }
        $storiesFile = $validatedData['file'];
        $fileSize = $storiesFile->getSize();
        $fileType = $storiesFile->getMimeType();
        if($fileSize <= 83886080){
            if($fileType == 'video/mp4' || $fileType == 'video/avi'){
                $fileTypeDB = 'video';
                $extension = 'mp4';
                $method = '/stories.getVideoUploadServer';
                $sendType = 'video_file';
            }else{
                $fileTypeDB = 'image';
                $extension = 'jpg';
                $method = '/stories.getPhotoUploadServer';
                $sendType = 'file';
            }
            $fileName = 'stories-' . date("Y-m-d-H-i-s") . ".".$extension;
            $userName = Auth::user()->login;
            Storage::disk('public')->putFileAs('files/users/'.$userName.'/stories', $storiesFile, $fileName);
            $getUpload = new Client([
                'base_uri' => 'https://api.vk.com',
            ]);
            $uploadParams = array(
                'v' => '5.131',
                'access_token' => $searchAccount->token,
                'add_to_news' => 0,
            );
            $uploadGet = $getUpload->request('GET', '/method'.$method, [
                'query' => $uploadParams
            ]);
            if ($uploadGet->getStatusCode() !== 200 || !$uploadGet->getBody()) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Ошибка запроса',
                    'message' => 'При отправке запроса на сервер ВК произошла ошибка'
                ]);
            }
            $uploadContent = $uploadGet->getBody()->getContents();
            $uploadJSON = json_decode($uploadContent);
            if (isset($uploadJSON->error)) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Ошибка получения токена',
                    'message' => 'При получении групп возникла ошибка'
                ]);
            }
            $sendClient = new Client();
            $sendStoriesPost = $sendClient->request('POST', $uploadJSON->response->upload_url, [
                'multipart' => [
                    [
                        'name' => $sendType,
                        'contents' => fopen('/storage/files/users/'.$userName.'/stories/'.$fileName, 'r'),
                    ]
                ]
            ]);
            if ($sendStoriesPost->getStatusCode() !== 200 || !$sendStoriesPost->getBody()) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Ошибка загрузки',
                    'message' => 'Ошибка при загрузке файла на ВК сервер'
                ]);
            }
            $sendStoriesContent = $sendStoriesPost->getBody()->getContents();
            $sendStoriesJSON = json_decode($sendStoriesContent);
            return response()->json([
                'status' => 'success',
                'title' => 'Крутяк',
                'message' => $sendStoriesJSON->response->upload_result
            ]);
            // Stories::create([
            //     'account_id' => $userId,
            //     'name' => $accountName,
            //     'link' => $accountLink,
            //     'avatar' => $accountAvatar,
            //     'token' => $token
            // ]); 
        }else{
            return response()->json([
                'status' => 'input',
                'name' => 'file',
                'message' => 'Максимальный размер файла 10МБ'
            ]);
        }
    }
}
