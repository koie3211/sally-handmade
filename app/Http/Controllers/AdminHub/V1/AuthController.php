<?php

namespace App\Http\Controllers\AdminHub\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\ForgotPasswordRequest;
use App\Http\Requests\AdminHub\V1\LoginRequest;
use App\Http\Requests\AdminHub\V1\RegisterRequest;
use App\Http\Requests\AdminHub\V1\ResetPasswordRequest;
use App\Models\AdminHub\AdminHubUser;
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
        ], $input->remember ?? false);

        abort_if(!$auth, 400, '帳密錯誤');

        $request->session()->regenerate();

        $user = auth('adminhub')->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? asset("uploads/adminhub/users/{$user->avatar}") : null,
            'email' => $user->email,
            'account' => $user->account,
            'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $input = $request->validated();

        $user = AdminHubUser::create($input);

        event(new Registered($user));

        auth('adminhub')->login($user);

        $request->session()->regenerate();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? asset("uploads/adminhub/users/{$user->avatar}") : null,
            'email' => $user->email,
            'account' => $user->account,
            'email_verified_at' => $user->email_verified_at?->toDateTimeString(),
        ]);
    }

    public function verifyEmail(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect('/admin');
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
            function (AdminHubUser $user, string $password) {
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
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? asset("uploads/adminhub/users/{$user->avatar}") : null,
            'email' => $user->email,
            'account' => $user->account,
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
