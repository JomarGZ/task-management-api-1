<?php

namespace App\Http\Resources\api\v1\tenants;

use App\Http\Resources\api\v1\Positions\PositionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->whenNotNull($this->id),
            'name'  => $this->whenNotNull($this->name),
            'email' => $this->whenNotNull($this->email),
            'role'  => $this->whenNotNull($this->role),
            'position'  => PositionResource::make($this->whenLoaded('position')),
            'avatar' => $this->whenLoaded('media', function () {
                return [
                    'thumb-200' => $this->getFirstMediaUrl('avatar', 'thumb-200'),
                    'thumb-60' => $this->getFirstMediaUrl('avatar', 'thumb-60'),
                ];
            }),
        ];
    }
}
