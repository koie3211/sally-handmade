<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (auth('budget')->check()) {
            return redirect()->route('budget.dashboard');
        }

        return view('budget.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! auth('budget')->attempt($credentials, $remember)) {
            return back()->withErrors(['email' => '帳號或密碼錯誤'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('budget.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        auth('budget')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('budget.login');
    }
}
