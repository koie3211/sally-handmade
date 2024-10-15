<?php

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'exam_subjects';

    public function questions()
    {
        return $this->hasMany(SelectQuestion::class);
    }
}
