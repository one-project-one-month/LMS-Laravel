<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ["user_id"];
    public function user()

    {
        return $this->belongsTo(User::class);

    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, "enrollments", "user_id", "course_id");
    }
}
