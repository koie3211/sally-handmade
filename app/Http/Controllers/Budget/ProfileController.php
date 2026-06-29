<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function showSettings(): View
    {
        return view('budget.settings');
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
