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
            "message" => "course retrive successfully",
            "data" => [
                "id" => $this->id,
                "courseName" => $this->course_name,
                "thumbnail" => $this->thumbnail ,

                "type" => $this->type,
                "level" => $this->level ?? "beginner",
                "description" => $this->description ?? "",
                "duration" => $this->duration,
                "originalPrice" => $this->original_price,
                "currentPrice" => $this->current_price ?? $this->original_price,
                "category" => CategoryResource::make($this->whenLoaded("category")),
                "instructorName" => $this->instructorUser->username,
                "instructorProfile" => $this->instructorUser->profile_photo,
                "instructorEducation" => $this->instructorUser->edu_background,
                "createdAt" => $this->created_at,
                "updatedAt" => $this->updated_at,

                "lessons" =>  LessonResource::collection($this->whenLoaded("lessons")),
                "socialLinks" => SocialLinkResource::make($this->whenLoaded("social_link"))
            ]
        ];
    }
}
