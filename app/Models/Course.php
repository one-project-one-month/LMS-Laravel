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


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter($query, $filter)
    {
        $query->when($filter['search']??false, function ($query,$search){
            $query->where(function($query) use ($search) {
                $query->where('course_name','like', '%' . $search . '%');
            });
        });

        $query->when($filter['type'] ?? false, function ($query, $search) {
            $query->where('type', 'like', '%' . $search . '%');
        });

        $query->when($filter['level'] ?? false, function ($query, $search) {
            $query->where('level', 'like', '%' . $search . '%');
        });

        $query->when($filter['category'] ?? false, function ($query, $search) {
            $query->whereHas('category', function ($query) use($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });


    public function socialLink(): HasOne
    {
        return $this->hasOne(SocialLink::class);

    }
}
