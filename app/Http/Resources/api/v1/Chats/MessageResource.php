<?php

namespace App\Http\Resources\api\v1\Chats;

use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->whenNotNull($this->id),
            'content' => $this->whenNotNull($this->content),
            'created_at' => $this->whenNotNull($this->created_at),
            'reply_count' => $this->whenNotNull($this->reply_count),
            'likes_by' => TenantMemberResource::collection($this->whenLoaded('likes')),
            'reaction_count' => $this->reaction_count,
            'is_liked' => $this->whenLoaded('likes', function () use ($request) {
                return $this->isLikeBy($request->user()?->id);
            }),
            'user' => TenantMemberResource::make(($this->whenLoaded('user'))),
        ];
    }
}
