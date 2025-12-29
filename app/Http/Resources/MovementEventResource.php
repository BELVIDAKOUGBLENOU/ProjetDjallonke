<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing('event');
        $eventField = $this->resource->event->toArray();
        $data = $eventField;
        $data = array_merge($data, parent::toArray($request));
        return $data;
    }
}
