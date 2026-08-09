<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\DatasetResource;
use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Services\GISService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function __construct(
        private readonly GISService $gisService,
    ) {}

    /**
     * List datasets for a project.
     *
     * @group Datasets
     *
     * Returns all imported datasets for the given project, ordered by import date (newest first).
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "project_id": 1,
     *       "imported_at": "2026-01-15T09:00:00.000000Z",
     *       "created_at": "2026-01-15T09:00:00.000000Z",
     *       "updated_at": "2026-01-15T09:00:00.000000Z"
     *     }
     *   ]
     * }
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $datasets = $project->datasets()->orderByDesc('imported_at')->get();

        return response()->json([
            'data' => DatasetResource::collection($datasets),
        ]);
    }

    /**
     * Get a specific dataset with its full GeoJSON data.
     *
     * @group Datasets
     *
     * @urlParam project integer required The project ID. Example: 1
     * @urlParam dataset integer required The dataset ID. Example: 1
     *
     * @response {
     *   "data": {
     *     "id": 1,
     *     "project_id": 1,
     *     "geojson": {
     *       "fiber_segments": [...],
     *       "nodes": [...],
     *       "splitters": [...]
     *     },
     *     "imported_at": "2026-01-15T09:00:00.000000Z",
     *     "created_at": "2026-01-15T09:00:00.000000Z",
     *     "updated_at": "2026-01-15T09:00:00.000000Z"
     *   }
     * }
     * @response 404 scenario="Not found" {"message": "Dataset not found."}
     */
    public function show(Project $project, ProjectDataset $dataset): JsonResponse
    {
        $this->authorize('view', $project);

        if ($dataset->project_id !== $project->id) {
            abort(404);
        }

        return response()->json([
            'data' => [
                'id' => $dataset->id,
                'project_id' => $dataset->project_id,
                'geojson' => $dataset->geojson,
                'imported_at' => $dataset->imported_at,
                'created_at' => $dataset->created_at,
                'updated_at' => $dataset->updated_at,
            ],
        ]);
    }

    /**
     * Test the PostGIS connection for a project.
     *
     * Verifies the connection credentials and lists the candidate schemas
     * containing expected fiber optic tables. Credentials are never stored.
     *
     * @group Datasets
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @bodyParam host string required The GIS server host. Example: 127.0.0.1
     * @bodyParam port integer required The GIS server port. Example: 5432
     * @bodyParam database string required The GIS database name. Example: fiberflow_gis
     * @bodyParam username string required The GIS username. Example: fiberflow
     * @bodyParam password string required The GIS password. Example: secret
     *
     * @response 200 {
     *   "data": {
     *     "schemas": ["apd_07", "apd_08"]
     *   }
     * }
     * @response 422 scenario="Connection failed" {"message": "Could not connect to the GIS database.", "errors": {"connection": ["Could not connect to the GIS database."]}}
     */
    public function testConnection(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate($this->connectionRules());

        $connection = $this->buildConnection($validated);

        if (! $this->gisService->testConnection($connection)) {
            return response()->json([
                'message' => __('Could not connect to the GIS database.'),
                'errors' => ['connection' => [__('Could not connect to the GIS database.')]],
            ], 422);
        }

        $schemas = $this->gisService->getAvailableSchemas($connection)->pluck('schema')->values();

        return response()->json([
            'data' => ['schemas' => $schemas],
        ]);
    }

    /**
     * Import a new dataset from PostGIS into the project.
     *
     * Connects with the provided credentials, fetches GIS data from the
     * specified schema and stores it as a GeoJSON dataset. The connection
     * details (except the password) are saved on the project.
     *
     * @group Datasets
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @bodyParam host string required The GIS server host. Example: 127.0.0.1
     * @bodyParam port integer required The GIS server port. Example: 5432
     * @bodyParam database string required The GIS database name. Example: fiberflow_gis
     * @bodyParam username string required The GIS username. Example: fiberflow
     * @bodyParam password string required The GIS password. Example: secret
     * @bodyParam schema string required The PostGIS schema to import from. Example: apd_08
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "project_id": 1,
     *     "imported_at": "2026-01-15T09:00:00.000000Z",
     *     "counts": {
     *       "fiber_segments": 150,
     *       "nodes": 45,
     *       "splitters": 12
     *     }
     *   }
     * }
     * @response 422 scenario="Invalid schema" {"message": "The selected schema is invalid.", "errors": {"schema": ["The selected schema is invalid."]}}
     * @response 422 scenario="Connection failed" {"message": "Could not connect to the GIS database.", "errors": {"connection": ["Could not connect to the GIS database."]}}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function import(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            ...$this->connectionRules(),
            'schema' => ['required', 'string', 'max:255'],
        ]);

        $connection = $this->buildConnection($validated);

        if (! $this->gisService->testConnection($connection)) {
            return response()->json([
                'message' => __('Could not connect to the GIS database.'),
                'errors' => ['connection' => [__('Could not connect to the GIS database.')]],
            ], 422);
        }

        $availableSchemas = $this->gisService->getAvailableSchemas($connection);

        if (! $availableSchemas->contains('schema', $connection['schema'])) {
            return response()->json([
                'message' => __('The selected schema is not available on this GIS server.'),
                'errors' => ['schema' => [__('The selected schema is not available on this GIS server.')]],
            ], 422);
        }

        $result = $this->gisService->importFromPostGIS($connection, $connection['schema']);

        $project->update([
            'gis_host' => $connection['host'],
            'gis_port' => $connection['port'],
            'gis_database' => $connection['database'],
            'gis_schema' => $connection['schema'],
            'gis_username' => $connection['username'],
        ]);

        $dataset = $project->datasets()->create([
            'geojson' => $result['geojson'],
            'imported_at' => now(),
        ]);

        $project->advanceTo(ProjectStatus::InProgress);

        return response()->json([
            'data' => [
                'id' => $dataset->id,
                'project_id' => $dataset->project_id,
                'imported_at' => $dataset->imported_at,
                'counts' => $result['counts'],
            ],
        ], 201);
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

    /**
     * Delete a dataset.
     *
     * Permanently deletes the dataset and its GeoJSON data.
     *
     * @group Datasets
     *
     * @urlParam project integer required The project ID. Example: 1
     * @urlParam dataset integer required The dataset ID. Example: 1
     *
     * @response 204 scenario="Deleted successfully"
     * @response 404 scenario="Not found" {"message": "Dataset not found."}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function destroy(Project $project, ProjectDataset $dataset): JsonResponse
    {
        $this->authorize('update', $project);

        if ($dataset->project_id !== $project->id) {
            abort(404);
        }

        if (Audit::where('projectdataset_id', $dataset->id)->exists()) {
            abort(422, 'Cannot delete dataset: audits exist for it.');
        }

        $dataset->delete();

        return response()->json(null, 204);
    }
}
