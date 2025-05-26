<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[

            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'dob' =>$this->dob,
            'address' => $this->address,
            'profile_photo' =>  url("/storage/" .  $this->profile_photo) ,
            'role_id' => $this->role_id,
            'edu_background' => $this->edu_background,
            'is_available' => $this->is_available,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'laravel_through_key' => $this->laravel_through_key,
        ];
    }
}
