<?php

namespace App\Models;

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

    public function scopeFilter($query, $filter)
    {

        $query->when($filter['search'] ?? false, function ($query, $value) {
            $query->whereHas("user", function ($query) use ($value) {
                $query->where('username', 'like', '%' . $value . '%')
                    ->orWhere('email', 'like', '%' . $value . '%')
                    ->orWhere('phone', 'like', '%' . $value . '%')
                    ->orWhere('address', 'like', '%' . $value . '%');
            })->orWhere("nrc", "like", "%" . $value . "%");
        });




        $query->when($filter['edu'] ?? false, function ($query, $value) {
            $query->where('edu_background', "like", "%{$value}%");
        });
        $query->when($filter['is_available'] ?? false, function ($query, $value) {
            $query->where('is_available', $value);
        });
    }
}
