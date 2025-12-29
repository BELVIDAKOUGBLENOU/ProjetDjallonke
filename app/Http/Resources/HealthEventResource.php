<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //load ifmissing event relation
        $this->loadMissing('event');
        $eventField = $this->resource->event->toArray();
        $data = $eventField;
        // merge arrays
        $data = array_merge($data, parent::toArray($request));
        return $data;
    }
}
