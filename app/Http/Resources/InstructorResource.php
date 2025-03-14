<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'user'           => [
                'id'             => $this->user->id,
                'username'       => $this->user->username,
                'email'          => $this->user->email,
                'phone'          => $this->user->phone,
                'dob'            => $this->user->dob,
                'address'        => $this->user->address,
                'profile_photo'  => $this->user->profile_photo,
                'is_available'   => $this->user->is_available,
            ],
            'nrc'            => $this->nrc,
            'edu_background' => $this->edu_background,
            'courses'        => CourseResource::collection($this->whenLoaded('courses')),
        ];
    }
}
