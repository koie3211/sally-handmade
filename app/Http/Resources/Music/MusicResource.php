<?php

namespace App\Http\Resources\Music;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MusicResource extends JsonResource
{
    public function toArray($request)
    {
        if ($request->is('music/*/admin/music') || $request->is('music/*/admin/music/*')) {
            return [
                'id' => $this->id,
                'music_type_id' => $this->music_type_id,
                'name' => $this->name,
                'composer' => $this->composer,
                'file' => Storage::disk('music')->url($this->file),
                'image' => $this->image ? Storage::disk('music')->url($this->image) : null,
                'sort' => $this->sort ?? 0,
                'status' => $this->status ?? true,
                'watched' => $this->watched ?? 0,
            ];
        }

        if ($request->is('music/*/music') || $request->is('music/*/music/*')) {
            return [
                'id' => $this->id,
                'type' => $this->musicType->name,
                'name' => $this->name,
                'composer' => $this->composer,
                'file' => Storage::disk('music')->url($this->file),
                'image' => $this->image ? Storage::disk('music')->url($this->image) : null,
                'watched' => $this->watched ?? 0,
            ];
        }
    }
}
