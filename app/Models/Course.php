<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
  protected $guarded=[];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
