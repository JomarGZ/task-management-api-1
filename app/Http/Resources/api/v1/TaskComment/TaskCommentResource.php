<?php

namespace App\Http\Resources\api\v1\TaskComment;

use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author' => TenantMemberResource::make($this->whenLoaded('author')),
        ];
    }
}
