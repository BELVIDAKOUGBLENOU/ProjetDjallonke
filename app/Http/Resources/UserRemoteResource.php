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
        $data = parent::toArray($request);
        $data['roles'] = $this->roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
            ];
        });
        $data['permissions'] = $this->permissions->map(function ($perm) {
            return [
                'id' => $perm->id,
                'name' => $perm->name,
            ];
        });
        $data['created_at'] = $this->created_at->format('Y-m-d H:i:s');
        // return [

        //     'roles' => $this->roles->map(function ($role) {
        //         return [
        //             'id' => $role->id,
        //             'name' => $role->name,
        //             'guard_name' => $role->guard_name
        //         ];
        //     }),
        //     'permissions' => $this->permissions->map(function ($perm) {
        //         return [
        //             'id' => $perm->id,
        //             'name' => $perm->name
        //         ];
        //     }),

        // ];
        return $data;
    }
}
