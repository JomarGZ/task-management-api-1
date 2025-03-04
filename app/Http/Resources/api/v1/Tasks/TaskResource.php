<?php

namespace App\Http\Resources\api\v1\Tasks;

use App\Http\Resources\api\v1\Comments\CommentResource;
use App\Http\Resources\api\v1\Projects\ProjectResource;
use App\Http\Resources\api\v1\TaskComment\TaskCommentResource;
use App\Http\Resources\api\v1\Teams\TeamMemberResource;
use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_filter([
            'id'             => $this->whenSet($this->id),
            'title'          => $this->whenSet($this->title),
            'priority_level' => $this->whenSet($this->priority_level),
            'status'         => $this->whenSet($this->status),
            'deadline_at'    => $this->whenSet($this->deadline_at),
            'started_at'     => $this->whenSet($this->started_at),
            'completed_at'   => $this->whenSet($this->completed_at),
            'created_at'     => $this->whenSet($this->created_at),
            'description'    => $this->whenSet($this->description,  $this->description),
            'assigned_users' => TeamMemberResource::collection($this->whenLoaded('assignedUsers')),
            'project'        => ProjectResource::make($this->whenLoaded('project')),
            'comments'       => CommentResource::collection($this->whenLoaded('comments')),
            'photo_attachments' => $this->whenLoaded('media', function () {
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
