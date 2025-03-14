<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CourseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "message" => "success🎉",
            'data' => $this->collection,

        ];
    }
    public function paginationInformation($request, $paginated, $default)
    {
        // $default['links'] = [];
        // $default['meta'] = [];


        return $default;
    }
}
