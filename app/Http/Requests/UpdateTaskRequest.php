<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'project_id' => ['sometimes', 'exists:projects,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'users' => ['sometimes', 'array'],
            'users.*' => ['exists:users,id'],
            'priority' => ['sometimes', new Enum(TaskPriority::class)],
            'status' => ['sometimes', new Enum(TaskStatus::class)],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'due_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'estimated_hours' => ['sometimes', 'numeric', 'min:0.5'],
            'estimate_minutes' => ['sometimes', 'integer', 'min:30'],
            'actual_minutes' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('estimated_hours') && $this->has('estimate_minutes')) {
                $validator->errors()->add('estimated_hours', 'Cannot provide both estimated_hours and estimate_minutes');
                $validator->errors()->add('estimate_minutes', 'Cannot provide both estimated_hours and estimate_minutes');
            }
        });
        return $validator;
    }

    protected function prepareForValidation(): void
    {
        // Convert estimated_hours to estimate_minutes after validation
        // This is done in the controller/service to handle conversion after validation passes
    }
}
