<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerUpdateRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:16'],
            'image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '頁面名稱',
            'image' => '圖片',
            'status' => '狀態',
        ];
    }
}
