<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        if (array_key_exists('owned_animals_count', $this->attributesToArray())) {
            $data['owned_animals_count'] = $this->owned_animals_count;
        }

        if (array_key_exists('related_animals_count', $this->attributesToArray())) {
            $data['related_animals_count'] = $this->related_animals_count;
        }

        return $data;
    }
}
