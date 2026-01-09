<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_uuid'  => $this['user_uuid'],
            'username'   => $this['username'],
            'created_at' => $this['created_at'],
            'roles'      => RoleResource::collection($this['roles'] ?? []),
        ];
    }
}
