<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;

class MusicLike extends Model
{
    protected $table = 'music_music_likes';

    public $timestamps = false;

    protected $fillable = [
        'music_id',
        'user_id',
    ];

    public function musics()
    {
        return $this->hasMany(Music::class);
    }
}
