<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        unset($data['password'], $data['remember_token'], $data['created_at'], $data['updated_at']);
        $communities = $this->mobileAppCommunities()->get();
        $data['communities'] = CommunityResource::collection($communities);
        return $data;
    }
}
