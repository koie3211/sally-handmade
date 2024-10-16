<?php

namespace App\Http\Controllers\Music\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Music\MusicType as MusicTypeReq;
use App\Http\Resources\Music\MusicTypeResource;
use App\Models\Music\MusicType;

class MusicTypeController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => MusicTypeResource::collection(MusicType::get()),
        ]);
    }

    public function store(MusicTypeReq $request)
    {
        $musicType = MusicType::create($request->validated());

        return response()->json([
            'status' => true,
            'data' => new MusicTypeResource($musicType),
        ]);
    }

    public function show(MusicType $musicType)
    {
        return response()->json([
            'status' => true,
            'data' => new MusicTypeResource($musicType),
        ]);
    }

    public function update(MusicTypeReq $request, MusicType $musicType)
    {
        $musicType->update($request->validated());

        return response()->json([
            'status' => true,
            'data' => new MusicTypeResource($musicType),
        ]);
    }

    public function destroy(MusicType $musicType)
    {
        if ($musicType->musics->isNotEmpty()) {
            return response()->json([
                'status' => false,
                'message' => '尚有該曲風的音樂存在',
            ], 400);
        }

        return response()->json([
            'status' => $musicType->delete(),
        ]);
    }
}
