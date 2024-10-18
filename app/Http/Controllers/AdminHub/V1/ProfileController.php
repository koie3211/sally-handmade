<?php

namespace App\Http\Controllers\AdminHub\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'name' => $user->name,
            'avatar' => $user->avatar ? asset("uploads/adminhub/users/{$user->avatar}") : null,
            'email' => $user->email,
            'account' => $user->account,
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $input = $request->safe();

        $user = $request->user();

        $user->update($input->except('avatar', 'current_password', 'password'));

        if ($input->has('avatar')) {
            $user->update([
                'avatar' => $input->avatar->store('adminhub/avatars', 'adminhub'),
            ]);
        }

        if ($input->has('password')) {
            $user->update([
                'password' => $input->password,
            ]);
        }

        return response()->json([
            'name' => $user->name,
            'avatar' => $user->avatar ? asset("uploads/adminhub/users/{$user->avatar}") : null,
            'email' => $user->email,
            'account' => $user->account,
        ]);
    }
}
