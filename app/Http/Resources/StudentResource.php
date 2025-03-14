<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'user'    => [
                'id'            => $this->user->id,
                'username'      => $this->user->username,
                'email'         => $this->user->email,
                'phone'         => $this->user->phone,
                'dob'           => $this->user->dob,
                'address'       => $this->user->address,
                'profile_photo' => $this->user->profile_photo,
                'is_available'  => $this->user->is_available,
            ],
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
