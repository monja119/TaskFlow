<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'user_id' => ['sometimes', 'exists:users,id'],
            'users' => ['sometimes', 'array'],
            'users.*' => ['exists:users,id'],
            'priority' => ['required', new Enum(TaskPriority::class)],
            'status' => ['required', new Enum(TaskStatus::class)],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'estimated_hours' => ['sometimes', 'numeric', 'min:0.5'],
            'estimate_minutes' => ['sometimes', 'integer', 'min:30'],
            'actual_minutes' => ['nullable', 'integer', 'min:1'],
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
        // This is done in the prepareForValidation to handle conversion before controller receives data
    }
}
