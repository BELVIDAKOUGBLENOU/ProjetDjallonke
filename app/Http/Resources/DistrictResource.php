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
        $data['uid'] = '' . $this->id;
        $model = $this->resource;
        if ($this->imbriqued) {
            $subDistricts = $this->whenLoaded('subDistricts');
            if (!$subDistricts instanceof \Illuminate\Http\Resources\MissingValue) {
                $subDistrictsResource = SubDistrictResource::collection($subDistricts);
                $subDistrictsResource->each(fn($r) => $r->setImbriqued($this->imbriqued));
                $data['sub_districts'] = $subDistrictsResource;
            }
        }
        // unset($data['created_at'], $data['updated_at'], );

        return $data;
    }
}
