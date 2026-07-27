<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'projectdataset_id' => $this->projectdataset_id,
            'performed_by' => $this->performed_by,
            'project_type_at_audit' => $this->project_type_at_audit,
            'phase_at_audit' => $this->phase_at_audit,
            'status' => $this->status,
            'quality_score' => $this->quality_score,
            'connectivity_score' => $this->connectivity_score,
            'coherence_score' => $this->coherence_score,
            'capacity_score' => $this->capacity_score,
            'extensibility_score' => $this->extensibility_score,
            'network_statistics' => $this->network_statistics,
            'ai_summary' => $this->ai_summary,
            'recommendations' => $this->recommendations,
            'anomaly_count' => $this->anomaly_count,
            'critical_anomaly_count' => $this->critical_anomaly_count,
            'model_used' => $this->model_used,
            'tokens_used' => $this->tokens_used,
            'error_message' => $this->error_message,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'performer' => new UserResource($this->whenLoaded('performer')),
            'dataset' => new DatasetResource($this->whenLoaded('dataset')),
        ];
    }
}
