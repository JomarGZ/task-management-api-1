<?php

namespace App\Http\Resources\api\v1\Tasks;

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
        ];
    }
}
