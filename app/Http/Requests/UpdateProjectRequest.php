<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use App\Enums\StudyPhase;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'client' => ['sometimes', 'string', 'max:100'],
            'municipality' => ['sometimes', 'string', 'max:100'],
            'study_phase' => ['sometimes', 'string', 'in:'.implode(',', StudyPhase::values())],
            'status' => ['sometimes', 'string', 'in:'.implode(',', ProjectStatus::values())],
            'gis_project_id' => ['sometimes', 'string', 'max:100'],
            'parent_project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ];
    }
}
