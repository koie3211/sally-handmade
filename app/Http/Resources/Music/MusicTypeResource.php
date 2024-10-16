<?php

namespace App\Http\Resources\Music;

use Illuminate\Http\Resources\Json\JsonResource;

class MusicTypeResource extends JsonResource
{
    public function toArray($request)
    {
        if ($request->is('music/*/admin/music-type') || $request->is('music/*/admin/music-type/*')) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'color' => $this->color,
                'sort' => $this->sort ?? 0,
                'status' => $this->status ?? true,
            ];
        }

        if ($request->is('music/*/music-type') || $request->is('music/*/music-type/*')) {
            return [
                'id' => $this->id,
                'color' => $this->color,
                'name' => $this->name,
            ];
        }
    }
}
