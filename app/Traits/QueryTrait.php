<?php

namespace App\Traits;

trait QueryTrait
{
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
