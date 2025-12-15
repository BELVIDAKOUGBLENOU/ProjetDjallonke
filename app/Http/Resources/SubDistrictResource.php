<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubDistrictResource extends JsonResource
{
    public bool $imbriqued = false;

    public function setImbriqued(bool $imbriqued): static
    {
        $this->imbriqued = $imbriqued;

        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if ($this->imbriqued) {
            $villages = $this->whenLoaded('villages');
            if (! $villages instanceof \Illuminate\Http\Resources\MissingValue) {
                $villagesResource = VillageResource::collection($villages);
                $villagesResource->each(fn ($r) => $r->setImbriqued($this->imbriqued));
                $data['villages'] = $villagesResource;
            }
        }

        return $data;
    }
}
