<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', new Enum(ProjectStatus::class)],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'risk_score' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
