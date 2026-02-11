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
        $data['from_premises_uid'] = $this->fromPremises ? $this->fromPremises->uid : $this->resource->fromPremises->uid;

        $data['to_premises_uid'] = $this->toPremises ? $this->toPremises->uid : $this->resource->toPremises->uid;
        return $data;
    }
}
