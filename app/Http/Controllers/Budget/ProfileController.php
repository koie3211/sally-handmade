<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budget\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function showSettings(): View
    {
        $user = auth('budget')->user();
        $categories = Category::forUser($user->id);
        $defaults = $user->defaultBookkeeping();

        return view('budget.settings', compact('categories', 'defaults'));
    }

    public function updateDefaults(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'default_transaction_type' => ['required', 'in:expense,income'],
            'default_category_id' => ['required', 'integer', 'exists:budget_categories,id'],
        ], [
            'default_transaction_type.required' => '請選擇預設類型',
            'default_transaction_type.in' => '預設類型無效',
            'default_category_id.required' => '請選擇預設分類',
            'default_category_id.integer' => '預設分類無效',
            'default_category_id.exists' => '預設分類不存在',
        ]);

        $user = auth('budget')->user();
        $category = Category::forUser($user->id)->firstWhere('id', (int) $data['default_category_id']);

        if (! $category || $category->type !== $data['default_transaction_type']) {
            return back()
                ->withInput()
                ->withErrors(['default_category_id' => '分類與類型不符，或無法使用此分類']);
        }

        $user->update($data);

        return back()->with('defaults_success', '記帳預設已更新');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => '請輸入目前密碼',
            'password.required' => '請輸入新密碼',
            'password.min' => '新密碼至少需要 8 個字元',
            'password.confirmed' => '新密碼與確認密碼不符',
        ]);

        $user = auth('budget')->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => '目前密碼錯誤']);
        }

        $user->update(['password' => $request->password]);

        return back()->with('success', '密碼已成功更新');
    }
}
