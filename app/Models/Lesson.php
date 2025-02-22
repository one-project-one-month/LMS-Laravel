<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $guarded=[];

    public function course()
    {
        return $this->belongsTo(Lesson::class);
    }
}
