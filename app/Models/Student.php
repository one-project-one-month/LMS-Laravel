<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = ["user_id"];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, "enrollments", "user_id", "course_id");
    }
    public function scopeFilter($query, $filter)
    {

        $query->when($filter['search'] ?? false, function ($query, $value) {
            $query->whereHas("user", function ($query) use ($value) {

                $query->where('username', 'like', '%' . $value . '%')
                    ->orWhere('email', 'like', '%' . $value . '%')
                    ->orWhere('phone', 'like', '%' . $value . '%')
                    ->orWhere('address', 'like', '%' . $value . '%');
            });
        });



        $query->when($filter['is_available'] ?? false, function ($query, $value) {
            $query->where('is_available', $value);
        });
    }
}
