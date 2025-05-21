<?php

namespace App\Http\Resources\api\v1\Tasks;

use App\Http\Resources\api\v1\Comments\CommentResource;
use App\Http\Resources\api\v1\Projects\ProjectResource;
use App\Http\Resources\api\v1\TaskComment\TaskCommentResource;
use App\Http\Resources\api\v1\tasks\LinkResource;
use App\Http\Resources\api\v1\Teams\TeamMemberResource;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
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
            'id'             => $this->whenNotNull($this->id),
            'title'          => $this->whenNotNull($this->title),
            'priority_level' => $this->whenNotNull($this->priority_level),
            'status'         => $this->whenNotNull($this->status),
            'deadline_at'    => $this->whenNotNull($this->deadline_at),
            'started_at'     => $this->whenNotNull($this->started_at),
            'completed_at'   => $this->whenNotNull($this->completed_at),
            'created_at'     => $this->whenNotNull($this->created_at),
            'category'     => $this->whenNotNull($this->category),
            'description'    => $this->whenNotNull($this->description,  $this->description),
            'project'        => ProjectResource::make($this->whenLoaded('project')),
            'comments'       => CommentResource::collection($this->whenLoaded('comments')),
            'assigned_users' => TenantMemberResource::collection($this->whenLoaded('users')),
            'links' => LinkResource::collection($this->whenLoaded('links')),
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
