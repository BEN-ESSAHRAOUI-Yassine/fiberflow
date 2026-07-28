<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'projects_count' => $this->resource['projects_count'],
            'audits_count' => $this->resource['audits_count'],
            'average_quality_score' => $this->resource['average_quality_score'],
            'total_anomalies' => $this->resource['total_anomalies'],
            'total_critical_anomalies' => $this->resource['total_critical_anomalies'],
            'projects_by_type' => $this->resource['projects_by_type'],
            'projects_by_status' => $this->resource['projects_by_status'],
            'audits_by_status' => $this->resource['audits_by_status'],
            'recent_audits' => $this->resource['recent_audits'],
        ];
    }
}
