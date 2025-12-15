<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
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
            $subDistricts = $this->whenLoaded('subDistricts');
            if (! $subDistricts instanceof \Illuminate\Http\Resources\MissingValue) {
                $subDistrictsResource = SubDistrictResource::collection($subDistricts);
                $subDistrictsResource->each(fn ($r) => $r->setImbriqued($this->imbriqued));
                $data['sub_districts'] = $subDistrictsResource;
            }
        }

        return $data;
    }
}
