<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PremiseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        // $data['uid'] = '' . $this->id;
        // unset($data['created_at'], $data['updated_at'], );

        if ($this->whenLoaded('keepers')) {

            $data['keepers'] = PremiseKeeperResource::collection($this->keepers);
        } else {
            $data['keepers'] = [];
        }

        if ($this->whenLoaded('village')) {
            $data['village'] = VillageResource::make($this->village);
        } else {
            $data['village'] = null;
        }
        return $data;
    }
}
