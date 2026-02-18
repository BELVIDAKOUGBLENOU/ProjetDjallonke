<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRemoteResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'roles' => $this->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name
                ];
            }),
            'permissions' => $this->permissions->map(function ($perm) {
                return [
                    'id' => $perm->id,
                    'name' => $perm->name
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
