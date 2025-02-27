<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function instructor()
    {
        return $this->belongsTo(User::class);
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, "enrollments", "course_id", "user_id");
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function socialLink(): HasOne
    {
        return $this->hasOne(SocialLink::class);
    }
}
