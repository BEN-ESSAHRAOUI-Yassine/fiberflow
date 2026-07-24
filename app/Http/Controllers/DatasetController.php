<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\GISService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DatasetController extends Controller
{
    public function __construct(
        private readonly GISService $gisService,
    ) {}

    public function create(Project $project): View
    {
        $this->authorize('update', $project);

        $schemas = $this->gisService->getAvailableSchemas();

        return view('datasets.import', compact('project', 'schemas'));
    }

    public function import(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'schema' => ['required', 'string', 'in:apd_07,apd_08,rec_08'],
        ]);

        $result = $this->gisService->importFromPostGIS($validated['schema']);

        $project->datasets()->create([
            'geojson' => $result['geojson'],
            'imported_at' => now(),
        ]);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', __('Dataset imported successfully with :count features.', ['count' => array_sum($result['counts'])]));
    }
}
