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
            'id'            => $this->whenSet($this->id),
            'name'          => $this->whenSet($this->name),
            'description'   => $this->whenSet($this->description),
            'started_at'    => $this->whenSet($this->started_at),
            'ended_at'      => $this->whenSet($this->ended_at),
            'budget'        => $this->whenSet($this->budget),
            'status'        => $this->whenSet($this->status),
            'priority'      => $this->whenSet($this->priority),
            'client_name'   => $this->whenSet($this->client_name),
            'created_at'    => $this->whenSet($this->created_at),
            'team_assignee' => TeamResource::make($this->whenLoaded('teamAssignee')),
            'manager' => TenantMemberResource::make($this->whenLoaded('projectManager')),
            'assigned_members' => TenantMemberResource::collection($this->whenLoaded('assignedTeamMembers')),
        ];
    }
}
