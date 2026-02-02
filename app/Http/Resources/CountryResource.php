<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    public bool $imbriqued = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function setImbriqued(bool $imbriqued): static
    {
        $this->imbriqued = $imbriqued;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data["uid"] = '' . $this->id;
        if ($this->imbriqued) {
            $districts = $this->whenLoaded('districts');
            if (!$districts instanceof \Illuminate\Http\Resources\MissingValue) {
                $districtsResource = DistrictResource::collection($districts);
                $districtsResource->each(fn($r) => $r->setImbriqued($this->imbriqued));
                $data['districts'] = $districtsResource;
            }
        }
        unset($data['is_active']);

        return $data;
    }
}
