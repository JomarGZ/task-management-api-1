<?php

namespace App\Http\Resources\api\v1\Projects;

use App\Http\Resources\api\v1\Teams\TeamResource;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'created_at'    => $this->created_at,
            'team_assignee' => TeamResource::make($this->whenLoaded('teamAssignee')),
            'project_manager' => TenantMemberResource::make($this->whenLoaded('projectManager')),
            'assigned_members' => TenantMemberResource::collection($this->whenLoaded('assignedTeamMembers')),
        ];
    }
}
