<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoriesController extends Controller
{
    public function storiesCreate(Request $request)
    {   
        $rules = [
            'name' => 'required|string|max:20',
            'accType' => 'required|string|max:5',
            'accountId' => 'required|integer',
            'file' => 'required|file|size:10240',
            'duration' => 'required|integer'
        ];
        $messages = [
            'name.required' => 'Обязательное поле',
            'name.string' => 'Не является строкой',
            'name.max' => 'Максимум :max символов',
            'accType.required' => 'Неопределённый аккаунт',
            'accType.string' => 'Неопределённый аккаунт',
            'accType.max' => 'Неопределённый аккаунт',
            'accountId.required' => 'Обязательное поле',
            'accountId.integer' => 'Не является числом',
            'file.required' => 'Файл является обязательным',
            'file.file' => 'Файл не является файлом',
            'file.size' => 'Максимальный вес файла 10МБ',
            'duration.required' => 'Файл является обязательным',
            'duration.integer' => 'Ошибка длительности видео',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return response()->json([
                'status' => 'input',
                'title' => $firstErrorKey,
                'message' => $errors[$firstErrorKey][0]
            ]);
        }
        $validatedData = $validator->validated();
        return response()->json([
            'status' => 'success',
            'title' => 'Крутяк',
            'message' => $validatedData['duration']
        ]);
    }
}
