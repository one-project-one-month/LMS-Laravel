<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocilaLinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            "facebook" => $this->facebook,
            "x" => $this->x,
            "phone" => $this->phone,
            "telegram" => $this->telegram,
            "email" => $this->email
        ];
    }
}
