<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\ForgotPasswordRequest;
use App\Http\Requests\AdminHub\V1\Admin\LoginRequest;
use App\Http\Requests\AdminHub\V1\Admin\RegisterRequest;
use App\Http\Requests\AdminHub\V1\Admin\ResetPasswordRequest;
use App\Models\AdminHub\User;
use App\Models\AdminHub\UserGroup;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $input = $request->safe();

        $auth = auth('adminhub')->attempt([
            'account' => $input->account,
            'password' => $input->password,
            'status' => true,
            fn ($query) => $query->whereRelation('userGroup', 'status', true),
        ], $input->remember ?? false);

        abort_if(!$auth, 400, '帳密錯誤');

        $request->session()->regenerate();

        $user = auth('adminhub')->user()->loadCount('passwordLogs');

        $user->update(['last_login_at' => now()]);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? asset("adminhub/uploads/{$user->avatar}") : null,
            'email' => $user->email,
            'account' => $user->account,
            'is_password_changed' => (bool) $user->passwordLogs_count,
            'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $input = $request->validated();

        $userGroup = UserGroup::where('is_default', true)->where('status', true)->firstOrFail();

        $user = User::create([...$input, 'user_group_id' => $userGroup->id]);

        event(new Registered($user));

        event(new PasswordReset($user));

        auth('adminhub')->login($user);

        $request->session()->regenerate();

        $user->update(['last_login_at' => now()]);

        $user->loadCount('passwordLogs');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? asset("adminhub/uploads/{$user->avatar}") : null,
            'email' => $user->email,
            'account' => $user->account,
            'is_password_changed' => (bool) $user->passwordLogs_count,
            'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
        ]);
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->to('admin');
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => '驗證信已寄送',
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('adminhub')->sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'message' => trans($status),
        ], $status === Password::RESET_LINK_SENT ? 200 : 400);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('adminhub')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json([
            'message' => trans($status),
        ], $status === Password::PASSWORD_RESET ? 200 : 400);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->loadCount('passwordLogs');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? asset("adminhub/uploads/{$user->avatar}") : null,
            'email' => $user->email,
            'account' => $user->account,
            'is_password_changed' => (bool) $user->passwordLogs_count,
            'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        auth('adminhub')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json(null, 204);
    }
}
