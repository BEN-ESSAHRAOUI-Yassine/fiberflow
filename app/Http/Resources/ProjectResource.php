<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'client' => $this->client,
            'municipality' => $this->municipality,
            'project_type' => $this->project_type,
            'study_phase' => $this->study_phase,
            'gis_project_id' => $this->gis_project_id,
            'status' => $this->status,
            'parent_project_id' => $this->parent_project_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'parent' => new ProjectResource($this->whenLoaded('parentProject')),
            'children' => ProjectResource::collection($this->whenLoaded('childProjects')),
        ];
    }
}
