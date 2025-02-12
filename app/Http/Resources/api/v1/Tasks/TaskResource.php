<?php

namespace App\Http\Resources\api\v1\Tasks;

use App\Http\Resources\api\v1\Projects\ProjectResource;
use App\Http\Resources\api\v1\TaskComment\TaskCommentResource;
use App\Http\Resources\api\v1\Teams\TeamMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_filter([
            'id'             => $this->when(isset($this->id), $this->id),
            'title'          => $this->when(isset($this->title), $this->title),
            'description'    => $this->when(isset($this->description), Str::limit($this->description, 50, '...')),
            'priority_level' => $this->when(isset($this->priority_level), $this->priority_level),
            'status'         => $this->when(isset($this->status), $this->status),
            'deadline_at'    => $this->when(isset($this->deadline_at), $this->deadline_at),
            'started_at'     => $this->when(isset($this->started_at), $this->started_at),
            'completed_at'   => $this->when(isset($this->completed_at), $this->completed_at),
            'created_at'     => $this->when(isset($this->created_at), $this->created_at),
            'assigned_users' => TeamMemberResource::collection($this->whenLoaded('assignedUsers')),
            'project'        => ProjectResource::make($this->whenLoaded('project')),
            'comments'       => TaskCommentResource::collection($this->whenLoaded('comments')),
            'photo_attachments' => $this->when(isset($this->id), function () {
                return $this->getMedia('task_attachments')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'name' => $media->file_name,
                        'size' => $media->size,
                        'mime_type' => $media->mime_type,
                    ];
                });
            }),
        ], fn($value) => !is_null($value));
    }
}
