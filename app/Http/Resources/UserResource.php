<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class UserResource extends JsonResource
{
    public bool $toRemote = false;

    /**
     * Set the toRemote flag.
     *
     * @param  bool  $toRemote
     * @return $this
     */
    public function setToRemote(bool $toRemote): self
    {
        $this->toRemote = $toRemote;
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
        unset($data['password'], $data['remember_token'], $data['created_at'], $data['updated_at']);
        if (!$this->toRemote) {

            $communities = $this->mobileAppCommunities()->get();
        } else {
            $communities = $this->communities()->get();
        }

        $data['communities'] = CommunityResource::collection($communities);
        // generale permissions
        $oldPid = getPermissionsTeamId();
        setPermissionsTeamId(0);
        $data['globalRoles'] = $this->getRoleNames()->toArray();
        $data['globalPermissions'] = $this->getAllPermissions()->pluck('name')->toArray();
        setPermissionsTeamId($oldPid);
        $data['remote_id'] = $this->toRemote;

        return $data;
    }
}
