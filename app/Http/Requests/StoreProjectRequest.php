<?php

namespace App\Http\Requests;

use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'client' => ['required', 'string', 'max:100'],
            'municipality' => ['required', 'string', 'max:100'],
            'project_type' => ['required', 'string', 'in:'.implode(',', ProjectType::values())],
            'study_phase' => ['required', 'string', 'in:'.implode(',', StudyPhase::values())],
            'gis_project_id' => ['required', 'string', 'max:100'],
            'parent_project_id' => [
                'nullable',
                'integer',
                Rule::exists('projects', 'id')
                    ->where('project_type', ProjectType::Transport->value)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
