<?php

namespace App\Http\Resources\api\v1\Tasks;

use App\Http\Resources\api\v1\Projects\ProjectResource;
use App\Http\Resources\api\v1\TaskComment\TaskCommentResource;
use App\Http\Resources\api\v1\Teams\TeamMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'description'       => $this->description,
            'priority_level'    => $this->priority_level,
            'status'            => $this->status,
            'deadline_at'       => $this->deadline_at,
            'started_at'        => $this->started_at,
            'completed_at'      => $this->completed_at,
            'created_at'        => $this->created_at,
            'project'           => ProjectResource::make($this->whenLoaded('project')),
            'assigned_member'   => TeamMemberResource::make($this->whenLoaded('assignee')),
            'comments'          => TaskCommentResource::collection($this->whenLoaded('comments'))
        ];
    }
}
