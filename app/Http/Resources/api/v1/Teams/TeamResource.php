<?php

namespace App\Http\Resources\api\v1\Teams;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class TeamResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->whenSet($this->id),
            'name'      => $this->wwhenSet($this->name),
            'members'   => TeamMemberResource::collection($this->whenLoaded('members')),
        ];
    }
}
