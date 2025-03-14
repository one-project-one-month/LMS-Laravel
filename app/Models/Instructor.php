<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instructor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function scopeFilter(Builder $query, array $filter)
    {
        $query->when($filter['search'] ?? false, function(Builder $query, $value) {
            $query->whereHas('user', function(Builder $query) use($value) {
                $query
                    ->where('username', 'like', '%' . $value . '%')
                    ->orWhere('email', 'like', '%' . $value . '%')
                    ->orWhere('phone', 'like', '%' . $value . '%')
                    ->orWhere('address', 'like', '%' . $value . '%');
            })->orWhere('nrc', 'like', '%' . $value . '%');
        });

        $query->when($filter['edu'] ?? false, function(Builder $query, $value) {
            $query->where('edu_background', 'like', '%' . $value . '%');
        });

        $query->when(isset($filter['is_available']), function(Builder $query) use($filter) {
            $query->whereHas('user', function(Builder $query) use($filter) {
                $query->whereHas('user', function(Builder $query) use($filter) {
                    $query->where('is_available', $filter['is_available']);
                });
            });
        });
    }
}
