<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'login'    => 'required|unique:users,login',
            'email'    => 'required|email:rfc,dns|unique:user_infos,email',
            'password' => 'required|min:8'
        ];
    }

    public function messages()
    {
        return [
            'login.required'     => 'Логин не может быть пустым.',
            'login.unique'       => 'Данный логин уже используется в системе.',
            'email.required'     => 'Вы не указали вашу E-Mail почту.',
            'email.email'        => 'Данный E-Mail не подходит, укажите другой.',
            'email.unique'       => 'Эта E-Mail почта уже используется в системе.',
            'password.required'  => 'Пароль обязателен для входа на сайт. Укажите его.',
            'password.min'       => 'Минимальная длина пароля :min символов.'
        ];
    }
}
