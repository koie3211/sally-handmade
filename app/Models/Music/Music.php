<?php

namespace App\Models\Music;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    protected $table = 'music_musics';

    protected $fillable = [
        'music_type_id',
        'name',
        'composer',
        'file',
        'image',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function musicType()
    {
        return $this->belongsTo(MusicType::class);
    }

    public function musicLikes()
    {
        return $this->hasMany(MusicLike::class);
    }
}
