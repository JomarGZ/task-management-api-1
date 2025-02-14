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
            'id'            => $this->when(isset($this->id), $this->id),
            'name'          => $this->when(isset($this->name), $this->name),
            'description'   => $this->when(isset($this->description), $this->description),
            'started_at'    => $this->when(isset($this->started_at), $this->started_at),
            'ended_at'      => $this->when(isset($this->ended_at), $this->ended_at),
            'budget'        => $this->when(isset($this->budget), $this->budget),
            'status'        => $this->when(isset($this->status), $this->status),
            'priority'      => $this->when(isset($this->priority), $this->priority),
            'client_name'   => $this->when(isset($this->client_name), $this->client_name),
            'created_at'    => $this->when(isset($this->created_at), $this->created_at),
            'team_assignee' => TeamResource::make($this->whenLoaded('teamAssignee')),
            'manager' => TenantMemberResource::make($this->whenLoaded('projectManager')),
            'assigned_members' => TenantMemberResource::collection($this->whenLoaded('assignedTeamMembers')),
        ];
    }
}
