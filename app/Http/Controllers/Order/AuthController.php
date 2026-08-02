<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (auth('order')->check()) {
            return redirect()->route('order.dashboard');
        }

        return view('order.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! auth('order')->attempt($credentials, $remember)) {
            return back()->withErrors(['email' => '帳號或密碼錯誤'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('order.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        auth('order')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('order.login');
    }
}
