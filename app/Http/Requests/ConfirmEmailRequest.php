<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmEmailRequest extends FormRequest
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
            'code'    => 'required|numeric|in:'.session('emailCode'),
        ];
    }

    public function messages()
    {
        return [
            'code.in'       => 'Код с вашего Email введён не верно.',
            'code.required' => 'Введите код, отправленный вам на Email.',
            'code.numeric'      => 'Код должен состоять только из чисел.',
        ];
    }
}
