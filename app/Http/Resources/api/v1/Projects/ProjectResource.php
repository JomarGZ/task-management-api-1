<?php

namespace App\Http\Resources\api\v1\Projects;

use App\Http\Resources\api\v1\Teams\TeamResource;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class ProjectResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->whenHas($this->id),
            'name'          => $this->whenHas($this->name),
            'description'   => $this->whenHas($this->description),
            'started_at'    => $this->whenHas($this->started_at),
            'ended_at'      => $this->whenHas($this->ended_at),
            'budget'        => $this->whenHas($this->budget),
            'status'        => $this->whenHas($this->status),
            'priority'      => $this->whenHas($this->priority),
            'client_name'   => $this->whenHas($this->client_name),
            'created_at'    => $this->whenHas($this->created_at),
            'assigned_members' => TenantMemberResource::collection($this->whenLoaded('assignedTeamMembers')),
        ];
    }
}
