<?php

namespace App\Http\Controllers\Music\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Music\Music as MusicReq;
use App\Http\Resources\Music\MusicResource;
use App\Models\Music\Music;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MusicController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => MusicResource::collection(Music::get()),
        ]);
    }

    public function store(MusicReq $request)
    {
        $input = $request->validated();

        if ($request->hasFile('file')) {
            $input['file'] = $this->fileUpload('music', 'music', $request->file('file'));
        }

        if ($request->hasFile('image')) {
            $input['image'] = $this->fileUpload('music', 'image', $request->file('image'));
        }

        $music = Music::create($input);

        return response()->json([
            'status' => true,
            'data' => new MusicResource($music),
        ]);
    }

    public function show(Music $music)
    {
        return response()->json([
            'status' => true,
            'data' => new MusicResource($music),
        ]);
    }

    public function update(MusicReq $request, Music $music)
    {
        $input = $request->validated();

        if ($request->hasFile('file')) {
            $this->fileDelete('music', $music->file);
            $input['file'] = $this->fileUpload('music', 'music', $request->file('file'));
        }

        if ($request->hasFile('image')) {
            $this->fileDelete('music', $music->image);
            $input['image'] = $this->fileUpload('music', 'image', $request->file('image'));
        }

        $music->update($input);

        return response()->json([
            'status' => true,
            'data' => new MusicResource($music),
        ]);
    }

    public function destroy(Music $music)
    {
        $this->fileDelete('music', $music->file);
        $this->fileDelete('music', $music->image);

        return response()->json([
            'status' => $music->delete(),
        ]);
    }

    public function fileUpload($disk, $dir, $file)
    {
        $fileName = Str::random() . '.' . $file->getClientOriginalExtension();

        return Storage::disk($disk)->putFileAs($dir, $file, $fileName);
    }

    public function fileDelete($disk, $file)
    {
        Storage::disk($disk)->delete($file);
    }
}
