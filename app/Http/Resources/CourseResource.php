<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            "id" => $this->id,
            "course_name" => $this->course_name,
            "thumbnail" => url("/storage/" .  $this->thumbnail),
            "type" => $this->type,
            "level" => $this->level ?? "beginner",
            "description" => $this->description ?? "",
            "duration" => $this->duration,
            "original_price" => $this->original_price,
            "current_price" => $this->current_price ?? $this->original_price,
            "category" => CategoryResource::make($this->whenLoaded("category")),
            "instructor" =>$this->instructorUser ,
            "instructorName" => $this->instructorUser->username,
            "instructorProfile" => $this->instructorUser->profile_photo ? url($this->instructorUser->profile_photo) : "https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg?t=st=1745687290~exp=1745690890~hmac=8a900b8b8cee8d963d5f5128578627883c9e4fca9928a3261b1ceb09328bb389&w=740",
            "instructorEducation" => $this->instructorUser->edu_background,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,

            "lessons" =>  LessonResource::collection($this->whenLoaded("lessons")),

            "socialLinks" => SocialLinkResource::make($this->whenLoaded("social_link"))
        ];
    }
}
