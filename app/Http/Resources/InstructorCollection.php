<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InstructorCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($instructor) {
            return [
                'id' => $instructor->id,
                'username' => $instructor->user->username,
                'email' => $instructor->user->email,
                'phone' => $instructor->user->phone,
                'dob' => $instructor->user->dob,
                'address' => $instructor->user->address,
                'nrc' => $instructor->nrc,
                'edu_background' => $instructor->edu_background,
                'profile_photo' => $instructor->user->profile_photo,
                // 'role' => $instructor->user->role
            ];
        })->toArray();
    }
}
