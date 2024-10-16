<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;

class MusicType extends Model
{
    protected $table = 'music_music_types';

    protected $fillable = [
        'name',
        'color',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function musics()
    {
        return $this->hasMany(Music::class);
    }
}
