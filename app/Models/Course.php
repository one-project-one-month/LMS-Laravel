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
        return $this->belongsTo(Instructor::class);
    }
    public function instructorUser()
    {
        return $this->hasOneThrough(User::class, Instructor::class, "id", "id", "instructor_id", "user_id");

        // User::class → The final model you want to access.
        // Instructor::class → The intermediate model.
        // 'id' (3rd argument) → The primary key of the Instructor model.
        // 'id' (4th argument) → The primary key of the User model.
        // 'instructor_id' (5th argument) → Foreign key in Course pointing to Instructor.
        // 'user_id' (6th argument) → Foreign key in Instructor pointing to User.

    }

    public function students()
    {
        return $this->belongsToMany(Student::class, "enrollments", "course_id", "user_id")
            ->withPivot('enrollment_date', 'is_completed', 'completed_date');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function social_link(): HasOne
    {
        return $this->hasOne(SocialLink::class);
    }

    public function scopeFilter($query, $filter)
    {

        $query->when($filter['search'] ?? false, function ($query, $value) {
            $query->where(function ($query) use ($value) {

                $query->where('course_name', 'like', '%' . $value . '%')->orWhere("description", "like", "%$value%");
            });
        });

        $query->when($filter['type'] ?? false, function ($query, $value) {
            $query->where('type', $value);
        });

        $query->when($filter['level'] ?? false, function ($query, $value) {
            $query->where('level', $value);
        });

        $query->when($filter['category'] ?? false, function ($query, $value) {
            $query->whereHas('category', function ($query) use ($value) {
                $query->where('name', 'like', '%' . $value . '%');
            });
        });
        $query->when($filter['instructor'] ?? false, function ($query, $value) {
            $query->whereHas('instructor.user', function ($query) use ($value) {
                $query->where('username', 'like', "%{$value}%");
            });
        });
        $query->when($filter['price'] ?? false, function ($query, $value) {

            $query->whereBetween('price', [$value - 50, $value + 50]);
        });
    }
}
