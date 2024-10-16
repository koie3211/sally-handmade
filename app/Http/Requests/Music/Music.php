<?php

namespace App\Http\Requests\Music;

use Illuminate\Foundation\Http\FormRequest;

class Music extends FormRequest
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
        switch ($this->method()) {
            case 'POST':
                return [
                    'music_type_id' => 'required|exists:music_music_types,id',
                    'name' => 'required|string|min:3|max:20',
                    'composer' => 'required|string|min:3|max:20',
                    'file' => 'required|file|max:5120',
                    'image' => 'nullable|file|max:2048',
                    'sort' => 'nullable|integer|min:0|max:255',
                    'status' => 'nullable|boolean',
                ];
            case 'PUT':
                return [
                    'music_type_id' => 'required|exists:music_music_types,id',
                    'name' => 'required|string|min:3|max:20',
                    'composer' => 'required|string|min:3|max:20',
                    'file' => 'nullable|file|max:5120',
                    'image' => 'nullable|file|max:2048',
                    'sort' => 'nullable|integer|min:0|max:255',
                    'status' => 'nullable|boolean',
                ];
        }
    }

    public function attributes()
    {
        return [
            'music_type_id' => '曲風',
            'name' => '歌名',
            'composer' => '作者',
            'file' => '音樂',
            'image' => '封面圖',
            'sort' => '排序',
            'status' => '狀態',
        ];
    }
}
