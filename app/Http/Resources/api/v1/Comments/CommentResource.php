<?php

namespace App\Http\Resources\api\v1\Comments;

use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenSet($this->id),
            'content' =>  $this->whenSet($this->content),
            'created_at' =>  $this->whenSet($this->created_at),
            'updated_at' =>  $this->whenSet($this->updated_at),
            'user' => TenantMemberResource::make($this->whenLoaded('user')),
            'replies' => CommentReplyResource::collection($this->whenLoaded('replies')),
        ];
    }
}
