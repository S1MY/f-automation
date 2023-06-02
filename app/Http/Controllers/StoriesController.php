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
            'accType' => 'required|string|in:user,group',
            'accountId' => 'required|integer',
            'file' => 'required',
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

        $fileType = $request->file->getMimeType();
        $fileSize = $request->file->getSize();
        return response()->json([
            'status' => 'success',
            'title' => 'Крутяк',
            'message' => $fileType.'|'.$fileSize
        ]);
    }
}
