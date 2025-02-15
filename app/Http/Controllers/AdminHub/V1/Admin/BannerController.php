<?php

namespace App\Http\Controllers\AdminHub\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminHub\V1\Admin\BannerStoreRequest;
use App\Http\Requests\AdminHub\V1\Admin\StatusRequest;
use App\Http\Requests\AdminHub\V1\Admin\UserUpdateRequest;
use App\Models\AdminHub\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Banner::class);

        $length = $request->integer('length', 35);

        $keyword = $request->query('keyword');

        $rows = Banner::query()
            ->when($keyword, fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$keyword}%")))
            ->orderBy('id')
            ->paginate($length);

        return response()->json([
            'data' => [
                'data' => $rows->map(fn ($row) => [
                    'id' => $row->id,
                    'name' => $row->name,
                    'image' => asset("adminhub/uploads/{$row->image}"),
                    'status' => $row->status,
                    'updated_at' => $row->updated_at->toDateTimeString(),
                ]),
                'count' => $rows->total(),
            ],
        ]);
    }

    public function store(BannerStoreRequest $request): JsonResponse
    {
        Gate::authorize('create', Banner::class);

        $input = $request->safe();

        $banner = Banner::create(
            $input->merge([
                'image' => $input->image->store('banners', 'adminhub'),
            ])->toArray()
        );

        return response()->json([
            'data' => [
                'id' => $banner->id,
                'name' => $banner->name,
                'image' => asset("adminhub/uploads/{$banner->image}"),
                'status' => $banner->status,
                'updated_at' => $banner->updated_at->toDateTimeString(),
            ],
        ], 201);
    }

    public function show(Request $request, Banner $banner): JsonResponse
    {
        Gate::authorize('view', $banner);

        return response()->json([
            'data' => [
                'id' => $banner->id,
                'page' => $banner->page,
                'name' => $banner->name,
                'image' => asset("adminhub/uploads/{$banner->image}"),
                'status' => $banner->status,
            ],
        ]);
    }

    public function update(UserUpdateRequest $request, Banner $banner): JsonResponse
    {
        Gate::authorize('update', $banner);

        $input = $request->safe();

        $banner->update($input->except('image'));

        if ($input->has('image')) {
            if ($banner->image) {
                Storage::disk('adminhub')->delete($banner->image);
            }

            $banner->update([
                'image' => $input->image->store('banners', 'adminhub'),
            ]);
        }

        return response()->json([
            'data' => [
                'id' => $banner->id,
                'name' => $banner->name,
                'image' => asset("adminhub/uploads/{$banner->image}"),
                'status' => $banner->status,
                'updated_at' => $banner->updated_at->toDateTimeString(),
            ],
        ]);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        Gate::authorize('delete', $banner);

        if ($banner->image) {
            Storage::disk('adminhub')->delete($banner->image);
        }

        $banner->delete();

        return response()->json(null, 204);
    }

    public function status(StatusRequest $request, Banner $banner): JsonResponse
    {
        Gate::authorize('update', $banner);

        $input = $request->safe();

        $banner->update([
            'status' => $input->status,
        ]);

        return response()->json(null, 204);
    }
}
