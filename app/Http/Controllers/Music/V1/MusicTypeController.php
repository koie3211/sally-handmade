<?php

namespace App\Http\Controllers\Music\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Music\MusicTypeResource;
use App\Models\Music\MusicType;

class MusicTypeController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => MusicTypeResource::collection(
                MusicType::where('status', true)->orderBy('sort', 'desc')->orderBy('id')->get(['id', 'name', 'color'])
            ),
        ]);
    }

    public function show(MusicType $musicType)
    {
        if (!$musicType->status) {
            return response()->json([
                'status' => false,
                'message' => '資料不存在',
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => new MusicTypeResource($musicType),
        ]);
    }
}
