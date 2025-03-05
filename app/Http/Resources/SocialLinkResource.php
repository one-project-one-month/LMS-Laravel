<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialLinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'facebook' => $this->when($this->facebook ?? false, $this->facebook),
            'x' => $this->when($this->x ?? false, $this->x),
            'telegram' => $this->when($this->telegram ?? false, $this->telegram),
            'phone' => $this->when($this->phone ?? false, $this->phone),
            'email' => $this->when($this->email ?? false, $this->email),
        ];
    }
}
