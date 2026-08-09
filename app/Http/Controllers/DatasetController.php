<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
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

        return view('datasets.import', [
            'project' => $project,
            'schemas' => collect(session()->get('schemas', []))
                ->map(fn ($schema) => is_string($schema)
                    ? (object) ['schema' => $schema, 'label' => $schema]
                    : (object) $schema),
        ]);
    }

    public function testConnection(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate($this->connectionRules());

        $connection = $this->buildConnection($validated);

        if (! $this->gisService->testConnection($connection)) {
            return back()
                ->withErrors(['connection' => __('Could not connect to the GIS database. Check the host, port, credentials and database name.')])
                ->withInput($request->except('password'));
        }

        $schemas = $this->gisService->getAvailableSchemas($connection);

        if ($schemas->isEmpty()) {
            return back()
                ->withErrors(['connection' => __('Connection succeeded, but no schema containing fiber optic tables was found on this server.')])
                ->with('connection_ok', true)
                ->withInput($request->except('password'));
        }

        return back()
            ->with('connection_ok', true)
            ->with('schemas', $schemas)
            ->with('success', __('Connection successful. :count schema(s) found.', ['count' => $schemas->count()]))
            ->withInput($request->except('password'));
    }

    public function import(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            ...$this->connectionRules(),
            'schema' => ['required', 'string', 'max:255'],
        ]);

        $connection = $this->buildConnection($validated);

        if (! $this->gisService->testConnection($connection)) {
            return back()
                ->withErrors(['connection' => __('Could not connect to the GIS database. Check the host, port, credentials and database name.')])
                ->withInput($request->except('password'));
        }

        $availableSchemas = $this->gisService->getAvailableSchemas($connection);

        if (! $availableSchemas->contains('schema', $connection['schema'])) {
            return back()
                ->withErrors(['schema' => __('The selected schema is not available on this GIS server.')])
                ->withInput($request->except('password'));
        }

        $result = $this->gisService->importFromPostGIS($connection, $connection['schema']);

        $project->update([
            'gis_host' => $connection['host'],
            'gis_port' => $connection['port'],
            'gis_database' => $connection['database'],
            'gis_schema' => $connection['schema'],
            'gis_username' => $connection['username'],
        ]);

        $project->datasets()->create([
            'geojson' => $result['geojson'],
            'imported_at' => now(),
        ]);

        $project->advanceTo(ProjectStatus::InProgress);

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', __('Dataset imported successfully with :count features.', ['count' => array_sum($result['counts'])]));
    }

    private function connectionRules(): array
    {
        return [
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'numeric', 'between:1,65535'],
            'database' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    private function buildConnection(array $validated): array
    {
        return [
            'host' => $validated['host'],
            'port' => $validated['port'],
            'database' => $validated['database'],
            'schema' => $validated['schema'] ?? null,
            'username' => $validated['username'],
            'password' => $validated['password'],
        ];
    }
}
