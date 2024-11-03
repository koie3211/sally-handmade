<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\StatusRequest;
use App\Http\Requests\AdminHub\V1\Admin\UserStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\UserUpdateRequest;
use App\Models\AdminHub\User;
use App\Models\AdminHub\UserGroup;
use App\Notifications\AdminHubUserRegistered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $currUser = $request->user();

        $length = $request->integer('length', 35);

        $keyword = $request->query('keyword');

        $rows = User::with('userGroup:id,name,level')
            ->where(fn ($query) => $query->where('id', $currUser->id)
                ->orWhereRelation('userGroup', 'level', '>', $currUser->userGroup->level))
            ->when($keyword, fn ($query) => $query->where('account', 'like', "%{$keyword}%")
                ->orWhere('name', 'like', "%{$keyword}%")->orWhere('email', 'like', "%{$keyword}%")
                ->orWhereRelation('userGroup', 'name', 'like', "%{$keyword}%"))
            ->orderByRaw("`id`='{$currUser->id}' DESC")->orderBy('id')
            ->paginate($length);

        // TODO: 增加判斷參數
        return response()->json([
            'data' => [
                'data' => $rows->map(fn ($row) => [
                    'id' => $row->id,
                    'user_group' => $row->userGroup->name,
                    'account' => $row->account,
                    'name' => $row->name,
                    'email' => $row->email,
                    'status' => $row->status,
                    'is_email_verified' => (bool) $row->email_verified_at,
                    'last_login_at' => $row->last_login_at?->toDateTimeString(),
                ]),
                'count' => $rows->total(),
            ],
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $currUserGroup = $request->user()->userGroup;

        return response()->json([
            'data' => [
                'user_groups' => UserGroup::where('level', '>', $currUserGroup->level)
                    ->orderBy('sort')->orderBy('id')->get(['id as value', 'name as label']),
            ],
        ]);
    }

    public function store(UserStoreRequest $request): JsonResponse
    {
        $input = $request->safe();

        $currUserGroup = $request->user()->userGroup;

        $userGroup = UserGroup::findOrFail($input->user_group_id);

        abort_if($userGroup->level <= $currUserGroup->level, 400, '群組權限錯誤，請重新設定');

        $password = Str::password(12);

        $user = User::create(
            $input->merge(['password' => $password])->except('avatar')
        );

        if ($input->has('avatar')) {
            $user->update([
                'avatar' => $input->avatar->store('avatars', 'adminhub'),
            ]);
        }

        $user->markEmailAsVerified();

        $user->notify(new AdminHubUserRegistered($password));

        return response()->json([
            'data' => [
                'id' => $user->id,
                'user_group' => $user->userGroup->name,
                'account' => $user->account,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'is_email_verified' => (bool) $user->email_verified_at,
                'last_login_at' => $user->last_login_at?->toDateTimeString(),
            ],
        ], 201);
    }

    public function edit(Request $request, User $user): JsonResponse
    {
        $currUser = $request->user()->load('userGroup');

        abort_if($user->userGroup->level <= $currUser->userGroup->level && $user->id !== $currUser->id, 403, '權限不足');

        return response()->json([
            'data' => [
                'user_groups' => UserGroup::where('level', '>', $currUser->userGroup->level)
                    ->orderBy('sort')->orderBy('id')->get(['id as value', 'name as label']),
            ],
        ]);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        $currUser = $request->user()->load('userGroup');

        abort_if($user->userGroup->level <= $currUser->userGroup->level && $user->id !== $currUser->id, 403, '權限不足');

        return response()->json([
            'data' => [
                'id' => $user->id,
                'user_group_id' => $user->user_group_id,
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
        $currUser = $request->user()->load('userGroup');

        abort_if($user->userGroup->level <= $currUser->userGroup->level && $user->id !== $currUser->id, 403, '權限不足');

        $input = $request->safe();

        $userGroup = UserGroup::findOrFail($input->user_group_id);

        abort_if($userGroup->level <= $currUser->userGroup->level, 400, '群組權限錯誤，請重新設定');

        $user->update($input->except($user->id === $currUser->id ? ['avatar', 'status'] : 'avatar'));

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
                'user_group' => $user->userGroup->name,
                'account' => $user->account,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'is_email_verified' => (bool) $user->email_verified_at,
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

    public function status(StatusRequest $request, User $user): JsonResponse
    {
        $input = $request->safe();

        $user->update([
            'status' => $input->status,
        ]);

        return response()->json(null, 204);
    }
}
