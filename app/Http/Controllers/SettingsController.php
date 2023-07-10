<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class SettingsController extends Controller
{
    public function changePassword(Request $request)
    {
        $rules = [
            'old_pass' => 'required|string',
            'new_pass' => 'required|string',
        ];
        $messages = [
            'old_pass.required' => 'Обязательное поле',
            'old_pass.string' => 'Не является строкой',
            'new_pass.required' => 'Обязательное поле',
            'new_pass.string' => 'Не является строкой',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('input', $firstErrorKey, $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();
        $user = User::where('id', '=', Auth::user()->id)->first();
        if(Hash::check($validatedData['old_pass'], $user->password)){
            if(Hash::check($validatedData['new_pass'], $user->password)){
                return $this->generateJsonResponse('error', 'Возникла ошибка', 'Новый пароль повторяет старый');
            }
            $user->password = Hash::make($validatedData['new_pass']);
            $user->save();
            return $this->generateJsonResponse('success', 'Всё прошло успешно', 'Пароль был успешно изменён');
        }else{
            return $this->generateJsonResponse('error', 'Возникла ошибка', 'Старый пароль не соответствует введённому вами значению');
        }
    }
    public function changeNewsletter(Request $request)
    {
        $rules = [
            'newsletter' => 'integer|in:1,0',
        ];
        $messages = [
            'newsletter.integer' => 'Должно быть числом',
            'newsletter.in' => 'Должно быть 1 или 0',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('error', 'Возникла ошибка', $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();
        $newsletter = ($validatedData['newsletter'] === 1) ? 1 : 0;
        $unewsletter = Auth::user()->UserInfo->newsletter;
        Auth::user()->UserInfo->newsletter = $newsletter;
        Auth::user()->UserInfo->save();
    }
}
