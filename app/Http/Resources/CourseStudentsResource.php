<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseStudentsResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'student_id' => $this->id,
            'name' => $this->user->username,
            'email' => $this->user->email,
            "phone" => $this->user->phone??null,
            "dob" => $this->user->dob??null,
            "address" => $this->user->address??null,
            "profile_photo" => $this->user->profile_photo??null,
            "is_available" => 1,
            'enrollment_date' => $this->pivot->enrollment_date,
            'is_completed' => (bool) $this->pivot->is_completed,
            'completed_date' => $this->pivot->completed_date,
        ];
    }
}
