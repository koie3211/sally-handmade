<?php

namespace App\Http\Requests\Music;

use Illuminate\Foundation\Http\FormRequest;

class Login extends FormRequest
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
            'email' => 'required|email|min:3|max:50',
            'password' => 'required|string|min:6|max:32',
        ];
    }

    public function attributes()
    {
        return [
            'email' => '信箱',
            'password' => '密碼',
        ];
    }
}
