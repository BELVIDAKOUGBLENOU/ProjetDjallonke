<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'event_date' => $this->event_date,
            'source' => $this->source,
            'comment' => $this->comment,
            'is_confirmed' => $this->is_confirmed,
            'version' => $this->version,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Creator info
            'created_by' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            // Animal info
            'animal' => $this->whenLoaded('animal', function () {
                return new AnimalResource($this->animal);
            }),
            // Specific type details
            'health_event' => $this->whenLoaded('healthEvent'),
            'movement_event' => $this->whenLoaded('movementEvent'),
            'transaction_event' => $this->whenLoaded('transactionEvent'),
            'reproduction_event' => $this->whenLoaded('reproductionEvent'),
            'birth_event' => $this->whenLoaded('birthEvent'),
            'milk_record' => $this->whenLoaded('milkRecord'),
            'death_event' => $this->whenLoaded('deathEvent'),
            'weight_record' => $this->whenLoaded('weightRecord'),
        ];
    }
}
