<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'start_date' => optional($this->start_date)?->toDateString(),
            'due_date' => optional($this->due_date)?->toDateString(),
            'completed_at' => optional($this->completed_at)?->toDateTimeString(),
            'archived_at' => optional($this->archived_at)?->toDateTimeString(),
            'estimate_minutes' => $this->estimate_minutes,
            'actual_minutes' => $this->actual_minutes,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => optional($this->created_at)?->toDateTimeString(),
            'updated_at' => optional($this->updated_at)?->toDateTimeString(),
        ];
    }
}
