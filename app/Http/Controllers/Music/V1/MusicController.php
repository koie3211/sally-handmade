<?php

namespace App\Http\Controllers\Music\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Music\MusicResource;
use App\Models\Music\Music;
use App\Models\Music\MusicLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MusicController extends Controller
{
    public function index(Request $request)
    {
        // 取得搜尋字串
        $search = $request->query('search', '');

        // 取得搜尋曲風
        $type = $request->query('type', '');

        // 排序欄位
        $orderBy = $request->query('order', 'sort');

        // 升降冪
        $sort = $request->query('sort', 'asc');

        // 每頁筆數
        $limit = (int) $request->input('limit', 18);

        return MusicResource::collection(
            Music::withCount('musicLikes')->with('musicType:id,name')
                ->whereHas('musicType', function ($query) use ($type) {
                    $query->where('status', true)->when($type, function ($query) use ($type) {
                        $query->where('id', $type);
                    });
                })
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")->orWhere('composer', 'like', "%{$search}%");
                })
                ->where('status', true)->orderBy($orderBy, $sort)->orderBy('id', 'desc')->paginate($limit)->withQueryString()
        );
    }

    public function show(Music $music)
    {
        $music->load('musicType');

        if (!$music->status || !$music->musicType->status) {
            return response()->json([
                'status' => false,
                'message' => '資料不存在',
            ]);
        }

        $music->increment('watched');

        return response()->json([
            'status' => true,
            'data' => new MusicResource($music),
        ]);
    }

    public function like(Music $music)
    {
        $user = Auth::guard('music_user')->user();

        if (MusicLike::where('user_id', $user->id)->where('music_id', $music->id)->doesntExist()) {
            $music->musicLikes()->create([
                'user_id' => $user->id,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Success',
        ]);
    }

    public function unlike(Music $music)
    {
        MusicLike::where('user_id', Auth::guard('music_user')->user()->id)->where('music_id', $music->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Success',
        ]);
    }

    public function likeList(Request $request)
    {
        $user = Auth::guard('music_user')->user();

        // 每頁筆數
        $limit = (int) $request->input('limit', 18);

        return MusicResource::collection(
            Music::with(['musicLikes', 'musicType:id,name'])->whereHas('musicLikes', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->whereHas('musicType', function ($query) {
                $query->where('status', true);
            })->where('status', true)->orderBy('sort')->orderBy('id', 'desc')->paginate($limit)->withQueryString()
        );
    }
}
