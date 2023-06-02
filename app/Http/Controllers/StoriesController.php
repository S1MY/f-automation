<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

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
        $storiesFile = $validatedData['file'];
        $fileSize = $storiesFile->getSize();
        $fileType = $storiesFile->getMimeType();
        if($fileSize <= 83886080){
            if($fileType == 'video/mp4' || $fileType == 'video/avi'){
                $fileTypeDB = 'video';
                $fileName = 'stories' . date("Y-m-d-H-i-s") . ".mp4";
            }else{
                $fileTypeDB = 'image';
                $fileName = 'stories' . date("Y-m-d-H-i-s") . ".jpg";
            }
            $userName = Auth::user()->login;
            $storiesFile->originalName = $fileName;
            Storage::disk('local')->putFile('files/'.$userName.'/stories', $storiesFile);
        }else{
            return response()->json([
                'status' => 'input',
                'name' => 'file',
                'message' => 'Максимальный размер файла 10МБ'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'title' => 'Крутяк',
            'message' => 'Проверь сохранился ли файл'
        ]);
    }
}
