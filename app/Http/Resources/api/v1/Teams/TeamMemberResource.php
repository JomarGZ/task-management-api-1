<?php

namespace App\Http\Resources\api\v1\Teams;

use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class TeamMemberResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenSet($this->id),
            'name' => $this->whenSet($this->name),
        ];
    }
}
