<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardService $dashboard): View
    {
        $data = $dashboard->summary($request->user());

        $data['recent_audits'] = collect($data['recent_audits'])
            ->map(fn (array $audit) => [
                'id' => $audit['id'],
                'project_id' => $audit['project_id'],
                'project_name' => $audit['project_name'],
                'project_slug' => $audit['project_slug'],
                'status' => $audit['status'],
                'quality_score' => $audit['quality_score'],
                'anomaly_count' => $audit['anomaly_count'],
                'critical_anomaly_count' => $audit['critical_anomaly_count'],
                'performer_name' => $audit['performer_name'],
                'created_at' => $audit['created_at'],
            ])
            ->all();

        return view('dashboard', compact('data'));
    }
}
