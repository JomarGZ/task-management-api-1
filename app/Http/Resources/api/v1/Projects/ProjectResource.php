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
            'id'            => $this->whenNotNull($this->id),
            'name'          => $this->whenNotNull($this->name),
            'description'   => $this->whenNotNull($this->description),
            'started_at'    => $this->whenNotNull($this->started_at),
            'ended_at'      => $this->whenNotNull($this->ended_at),
            'budget'        => $this->whenNotNull($this->budget),
            'status'        => $this->whenNotNull($this->status),
            'priority'      => $this->whenNotNull($this->priority),
            'client_name'   => $this->whenNotNull($this->client_name),
            'created_at'    => $this->whenNotNull($this->created_at),
            'assigned_members' => TenantMemberResource::collection($this->whenLoaded('assignedTeamMembers')),
        ];
    }
}
