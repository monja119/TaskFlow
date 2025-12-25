<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'project_id' => ['required', 'exists:projects,id'],
            'user_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'priority' => ['required', new Enum(TaskPriority::class)],
            'status' => ['required', new Enum(TaskStatus::class)],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'estimate_minutes' => [
                'nullable',
                'integer',
                'min:1',
                Rule::prohibitedIf(fn () => $this->filled('estimated_hours')),
            ],
            'estimated_hours' => [
                'nullable',
                'numeric',
                'min:0',
                Rule::prohibitedIf(fn () => $this->filled('estimate_minutes')),
            ],
            'actual_minutes' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
