<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
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
            'email'    => 'required|email:rfc,dns|exists:user_infos,email',
        ];
    }

    public function messages()
    {
        return [
            'email.required'     => 'Вы не указали вашу E-Mail почту.',
            'email.email'        => 'Данный E-Mail не подходит, укажите другой.',
            'email.exists'       => 'Эта E-Mail не найден в системе.'
        ];
    }
}
