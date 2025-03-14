<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InstructorCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            // 'current_page' => $this->currentPage(),
            // 'last_page' => $this->lastPage(),
            // 'next_page_url' => $this->nextPageUrl(),
            // 'prev_page_url' => $this->previousPageUrl(),
            // 'per_page' => $this->perPage(),
            // 'total' => $this->total()
        ];
    }
}
