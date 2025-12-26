<?php

namespace App\DataTransferObjects;

use App\Enums\ProjectStatus;
use App\Http\Requests\ProjectIndexRequest;

class ProjectFilterData
{
    public function __construct(
        public readonly ?ProjectStatus $status,
        public readonly ?string $search,
        public readonly ?int $perPage,
    ) {}

    public static function fromRequest(ProjectIndexRequest $request): self
    {
        $data = $request->validated();

        return new self(
            status: isset($data['status']) ? ProjectStatus::from($data['status']) : null,
            search: $data['search'] ?? null,
            perPage: $data['per_page'] ?? null,
        );
    }
}
