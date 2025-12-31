<?php

namespace App\Http\Resources;

use App\Models\CommunityMembership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        unset($data['created_at'], $data['updated_at'], $data['created_by'], $data['updated_by']);
        if (!is_null($this->pivot)) {
            $user_id = $this->pivot->user_id ?? null;
            $community_id = $this->pivot->community_id ?? null;
            $data['role'] = $this->pivot?->role ?? null;
            $permissions = User::find($user_id)?->communityPermissions($community_id)->pluck('name')->toArray() ?? null;
            $data['permissions'] = $permissions ?? [];
            unset($data['pivot']);
        } else {
            $user_id = Auth::user()?->id ?? null;
            $community_id = $this->id;
            $membership = CommunityMembership::where('user_id', $user_id)
                ->where('community_id', $community_id)
                ->first();
            if ($user_id && $membership) {
                $data['role'] = $membership->role ?? null;
                $oldTeamId = getPermissionsTeamId();
                setPermissionsTeamId($community_id);
                $permissions = User::find($user_id)?->communityPermissions($community_id)->pluck('name')->toArray() ?? null;

                $data['permissions'] = $permissions ?? [];
                setPermissionsTeamId($oldTeamId);
            } else {
                $data['role'] = null;
                $data['permissions'] = [];
            }
        }
        $this->loadMissing('country');
        $data['country'] = CountryResource::make($this->whenLoaded('country'));
        return $data;
    }
}
