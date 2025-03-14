<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ["user_id"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, "enrollments", "user_id", "course_id")
                    ->withPivot('enrollment_date', 'is_completed', 'completed_date');
    }

    public function scopeFilter(Builder $query, $filter)
    {
        $query->when($filter['search'] ?? false, function ($query, $value) {
            $query->whereHas("user", function ($query) use ($value) {
                $query->where('username', 'like', '%' . $value . '%')
                      ->orWhere('email', 'like', '%' . $value . '%')
                      ->orWhere('phone', 'like', '%' . $value . '%')
                      ->orWhere('address', 'like', '%' . $value . '%');
            });
        });

        $query->when(isset($filter['is_available']), function ($query) use ($filter) {
            $query->whereHas('user', function ($query) use ($filter) {
                $query->where('is_available', $filter['is_available']);
            });
        });
    }

}
