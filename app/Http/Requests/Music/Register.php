<?php

namespace App\Http\Requests\Music;

use Illuminate\Foundation\Http\FormRequest;

class Register extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:10',
            'email' => 'required|email|min:3|max:50|unique:music_users',
            'password' => 'required|string|min:6|max:32|confirmed',
            'password_confirmation' => 'required|string|min:6|max:32',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '姓名',
            'email' => '信箱',
            'password' => '密碼',
            'password_confirmation' => '確認密碼',
        ];
    }
}
