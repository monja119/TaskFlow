<?php

namespace App\DataTransferObjects;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\TaskIndexRequest;

class TaskFilterData
{
    public function __construct(
        public readonly ?TaskStatus $status,
        public readonly ?TaskPriority $priority,
        public readonly ?int $projectId,
        public readonly ?int $userId,
        public readonly ?string $search,
        public readonly ?int $perPage,
    ) {
    }

    public static function fromRequest(TaskIndexRequest $request): self
    {
        $data = $request->validated();

        return new self(
            status: isset($data['status']) ? TaskStatus::from($data['status']) : null,
            priority: isset($data['priority']) ? TaskPriority::from($data['priority']) : null,
            projectId: $data['project_id'] ?? null,
            userId: $data['user_id'] ?? null,
            search: $data['search'] ?? null,
            perPage: $data['per_page'] ?? null,
        );
    }
}
