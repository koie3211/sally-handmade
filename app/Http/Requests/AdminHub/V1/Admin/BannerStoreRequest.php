<?php

namespace App\Http\Requests\AdminHub\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerStoreRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => ['required', 'string', 'max:16', 'exists:admin_hub_banners,id'],
            'name' => ['required', 'string', 'max:16'],
            'image' => ['required', 'image', 'max:2048'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'page' => '頁面',
            'name' => '頁面名稱',
            'image' => '圖片',
            'status' => '狀態',
        ];
    }
}
