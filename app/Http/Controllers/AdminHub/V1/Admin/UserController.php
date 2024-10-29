<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\UserStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\UserUpdateRequest;
use App\Models\AdminHub\User;
use App\Models\AdminHub\UserGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $length = $request->integer('length', 35);

        $keyword = $request->query('keyword');

        $rows = User::query()
            ->when($keyword, fn ($query) => $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")->orWhere('account', 'like', "%{$keyword}%"))
            ->paginate($length);

        return response()->json([
            'data' => [
                'data' => $rows->map(fn ($row) => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'avatar' => $row->avatar ? asset("adminhub/uploads/{$row->avatar}") : null,
                    'account' => $row->account,
                    'email' => $row->email,
                    'status' => $row->status,
                    'last_login_at' => $row->last_login_at?->toDateTimeString(),
                ]),
                'count' => $rows->total(),
            ],
        ]);
    }

    public function create(): JsonResponse
    {
        return response()->json([
            'data' => [
                'user_groups' => UserGroup::where('status', true)
                    ->orderBy('sort')->orderBy('id')->get(['id as value', 'name as label']),
            ],
        ]);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $input = $request->safe();

        $password = Str::password(12);

        $user = User::create(
            $input->merge(['password' => $password])->except('avatar')
        );

        if ($input->has('avatar')) {
            $user->update([
                'avatar' => $input->avatar->store('avatars', 'adminhub'),
            ]);
        }

        // TODO: 寄預設密碼信
        info([$user->id, $password]);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ? asset("adminhub/uploads/{$user->avatar}") : null,
                'account' => $user->account,
                'email' => $user->email,
                'status' => $user->status,
                'last_login_at' => $user->last_login_at?->toDateTimeString(),
            ],
        ], 201);
    }

    public function edit(User $user): JsonResponse
    {
        return response()->json([
            'data' => [
                'user_groups' => UserGroup::where('id', $user->user_group_id)->orWhere('status', true)
                    ->orderBy('sort')->orderBy('id')->get(['id as value', 'name as label']),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ? asset("adminhub/uploads/{$user->avatar}") : null,
                'account' => $user->account,
                'email' => $user->email,
                'status' => $user->status,
            ],
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        $input = $request->safe();

        $user->update($input->except('avatar'));

        if ($input->has('avatar')) {
            if ($user->avatar) {
                Storage::disk('adminhub')->delete($user->avatar);
            }

            $user->update([
                'avatar' => $input->avatar->store('avatars', 'adminhub'),
            ]);
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar ? asset("adminhub/uploads/{$user->avatar}") : null,
                'account' => $user->account,
                'email' => $user->email,
                'status' => $user->status,
                'last_login_at' => $user->last_login_at?->toDateTimeString(),
            ],
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->avatar) {
            Storage::disk('adminhub')->delete($user->avatar);
        }

        $user->delete();

        return response()->json(null, 204);
    }
}
