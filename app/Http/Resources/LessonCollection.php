<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LessonCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($lesson) {
            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'description' => Str::limit($lesson->lesson_detail, 50, '...')
            ];
        })->toArray();
    }
}
