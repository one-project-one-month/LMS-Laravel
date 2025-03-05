<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                'id' => $this->id,
                'username' => $this->user->username,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'dob' => $this->user->dob,
                'address' => $this->user->address,
                'nrc' => $this->nrc,
                'edu_background' => $this->edu_background,
                'profile_photo' => $this->user->profile_photo,
                // 'role' => $this->user->role
            ];
    }
}
