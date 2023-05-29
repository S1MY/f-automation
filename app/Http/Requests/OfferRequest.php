<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
            'offer'    => 'required|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'offer.required'     => 'Нельзя отправить пустое предложение.',
            'offer.max'  => 'Предложение не должно привышать :max символов',
        ];
    }
}
