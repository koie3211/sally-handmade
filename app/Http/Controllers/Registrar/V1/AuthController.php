<?php

namespace App\Http\Controllers\Registrar\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registrar\V1\RegistrarLoginRequest;
use App\Models\Registrar\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Passkeys\Passkey;

class AuthController extends Controller
{
    public function login(RegistrarLoginRequest $request): JsonResponse
    {
        $input = $request->safe();

        $authenticated = auth('registrar')->attempt([
            'account' => $input->account,
            'password' => $input->password,
            'status' => true,
        ], $input->remember ?? false);

        abort_if(! $authenticated, 400, '帳密錯誤');

        $request->session()->regenerate();

        $user = auth('registrar')->user();
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'data' => $this->userPayload($user),
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->userPayload($request->user('registrar')),
        ]);
    }

    public function passkeys(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user('registrar')->passkeys()
                ->latest()
                ->get(['id', 'name', 'last_used_at', 'created_at'])
                ->map(fn (Passkey $passkey) => [
                    'id' => $passkey->id,
                    'name' => $passkey->name,
                    'last_used_at' => $passkey->last_used_at?->toDateTimeString(),
                    'created_at' => $passkey->created_at?->toDateTimeString(),
                ]),
        ]);
    }

    public function deletePasskey(Request $request, Passkey $passkey): JsonResponse
    {
        abort_unless($passkey->user_id === $request->user('registrar')->getKey(), 403);

        $passkey->delete();

        return response()->json(null, 204);
    }

    public function logout(Request $request): JsonResponse
    {
        auth('registrar')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(null, 204);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'account' => $user->account,
            'last_login_at' => $user->last_login_at?->toDateTimeString(),
            'has_passkeys' => $user->hasPasskeysEnabled(),
        ];
    }
}
