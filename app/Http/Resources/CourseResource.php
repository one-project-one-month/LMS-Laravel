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
            "category" => CategoryResource::make($this->category),
            "instructor_user" => InstructorUserResource::make($this->instructorUser),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "students" =>  $this->students,
            "lessons" =>  LessonResource::collection($this->whenLoaded("lessons")),
            "is_available" => $this->is_available,

            "socialLinks" => SocialLinkResource::make($this->whenLoaded("social_link"))
        ];
    }
}
