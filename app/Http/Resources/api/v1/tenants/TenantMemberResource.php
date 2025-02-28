<?php

namespace App\Http\Resources\api\v1\tenants;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class TenantMemberResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->whenSet($this->id),
            'name'  => $this->whenSet($this->name),
            'email' => $this->whenSet($this->email),
            'role'  => $this->whenSet($this->role),
            'avatar' => $this->whenSet($this->getFirstMediaUrl('avatar'),function () {
                    return [
                        'thumb-200' => $this->getFirstMediaUrl('avatar', 'thumb-200'),
                        'thumb-60' => $this->getFirstMediaUrl('avatar', 'thumb-60'),
                    ];
            })
        ];
    }
}
