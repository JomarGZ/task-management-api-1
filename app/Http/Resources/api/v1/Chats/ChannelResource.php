<?php

namespace App\Http\Resources\api\v1\Chats;

use App\Enums\ChatTypeEnum;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Http\Resources\BaseJsonResource;
use App\Services\v1\TenantMemberService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends BaseJsonResource
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
            'name' => $this->whenNotNull( $this->name),
            'description' => $this->whenNotNull($this->description),
            'type' => $this->whenNotNull($this->type),
            'is_direct' => $this->type === ChatTypeEnum::DIRECT->value,
            'recipient' => $this->when(
                $this->type === ChatTypeEnum::DIRECT->value,
                fn() => TenantMemberResource::make($this->getRecipient())
            ),
            'unread_count' => $this->unread_messages_count ?? 0,
            'active' => $this->whenNotNull($this->active),
            'participants' => TenantMemberResource::collection($this->whenLoaded('participants'))
        ];
    }

    protected function getRecipient()
    {
        if (!$this->relationLoaded('participants')) {
            return null;
        }

        return $this->participants
            ->where('id', '!=', auth()->id())
            ->first();
    }
}
