<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectCollection;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(protected ProjectService $projectService) {}

    /**
     * List all projects the authenticated user can access.
     *
     * @group Projects
     *
     * Returns a paginated collection of projects with creator and parent loaded.
     *
     * @response {
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "FTTH Bordeaux Nord",
     *       "description": "Fiber deployment study for northern Bordeaux district",
     *       "client": "City of Bordeaux",
     *       "municipality": "Bordeaux",
     *       "project_type": "transport",
     *       "study_phase": "APD",
     *       "gis_project_id": "GIS-2026-001",
     *       "status": "in_progress",
     *       "parent_project_id": null,
     *       "created_by": 1,
     *       "created_at": "2026-01-15T10:30:00.000000Z",
     *       "updated_at": "2026-01-15T10:30:00.000000Z",
     *       "creator": {"id": 1, "name": "Jean Dupont"},
     *       "parent": null,
     *       "children": []
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        return new ProjectCollection($this->projectService->list(auth()->user()));
    }

    /**
     * Create a new project.
     *
     * Only admins can create projects.
     *
     * @group Projects
     *
     * @bodyParam name string required Project name. Max 150 chars. Example: FTTH Bordeaux Nord
     * @bodyParam description string Optional project description. Example: Fiber deployment study
     * @bodyParam client string required Client name. Max 100 chars. Example: City of Bordeaux
     * @bodyParam municipality string required Municipality name. Max 100 chars. Example: Bordeaux
     * @bodyParam project_type string required Project type. Enum: transport, distribution. Example: transport
     * @bodyParam study_phase string required Study phase. Enum: APS, APD, PRO, EXE, REC, FIN. Example: APD
     * @bodyParam gis_project_id string required GIS project identifier. Max 100 chars. Example: GIS-2026-001
     * @bodyParam parent_project_id integer Optional parent project ID. Example: 1
     *
     * @response 201 {
     *   "id": 1,
     *   "name": "FTTH Bordeaux Nord",
     *   "description": "Fiber deployment study for northern Bordeaux district",
     *   "client": "City of Bordeaux",
     *   "municipality": "Bordeaux",
     *   "project_type": "transport",
     *   "study_phase": "APD",
     *   "gis_project_id": "GIS-2026-001",
     *   "status": "draft",
     *   "created_by": 1,
     *   "created_at": "2026-01-15T10:30:00.000000Z",
     *   "updated_at": "2026-01-15T10:30:00.000000Z"
     * }
     * @response 422 scenario="Validation failed" {"message": "The name field is required.", "errors": {"name": ["The name field is required."]}}
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        $data = array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]);

        $project = $this->projectService->create($data);

        return new ProjectResource($project);
    }

    /**
     * Get a single project by ID.
     *
     * Includes creator, parent project, and child projects.
     *
     * @group Projects
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @response {
     *   "id": 1,
     *   "name": "FTTH Bordeaux Nord",
     *   "description": "Fiber deployment study for northern Bordeaux district",
     *   "client": "City of Bordeaux",
     *   "municipality": "Bordeaux",
     *   "project_type": "transport",
     *   "study_phase": "APD",
     *   "gis_project_id": "GIS-2026-001",
     *   "status": "in_progress",
     *   "parent_project_id": null,
     *   "created_by": 1,
     *   "created_at": "2026-01-15T10:30:00.000000Z",
     *   "updated_at": "2026-01-15T10:30:00.000000Z",
     *   "creator": {"id": 1, "name": "Jean Dupont", "email": "jean@example.com", "role": "ingenieur"},
     *   "parent": null,
     *   "children": []
     * }
     * @response 404 scenario="Not found" {"message": "Project not found."}
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return new ProjectResource($project->loadMissing(['creator', 'parentProject', 'childProjects']));
    }

    /**
     * Update an existing project.
     *
     * Only admins can update projects.
     *
     * @group Projects
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @bodyParam name string Optional project name. Max 150 chars. Example: FTTH Bordeaux Nord Updated
     * @bodyParam description string Optional project description. Example: Updated description
     * @bodyParam client string Optional client name. Max 100 chars. Example: City of Bordeaux
     * @bodyParam municipality string Optional municipality name. Max 100 chars. Example: Bordeaux
     * @bodyParam study_phase string Optional study phase. Enum: APS, APD, PRO, EXE, REC, FIN. Example: PRO
     * @bodyParam status string Optional project status. Enum: draft, in_progress, audited, validated, archived. Example: in_progress
     * @bodyParam gis_project_id string Optional GIS project identifier. Max 100 chars. Example: GIS-2026-001
     * @bodyParam parent_project_id integer Optional parent project ID. Example: null
     *
     * @response {
     *   "id": 1,
     *   "name": "FTTH Bordeaux Nord Updated",
     *   "description": "Updated description",
     *   "client": "City of Bordeaux",
     *   "municipality": "Bordeaux",
     *   "project_type": "transport",
     *   "study_phase": "PRO",
     *   "gis_project_id": "GIS-2026-001",
     *   "status": "in_progress",
     *   "created_at": "2026-01-15T10:30:00.000000Z",
     *   "updated_at": "2026-01-15T12:00:00.000000Z"
     * }
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     * @response 404 scenario="Not found" {"message": "Project not found."}
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project = $this->projectService->update($project, $request->validated());

        return new ProjectResource($project);
    }

    /**
     * Delete a project (soft delete).
     *
     * Only admins can delete projects. The project is soft-deleted and can be restored.
     *
     * @group Projects
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @response 204 scenario="Deleted successfully"
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     * @response 404 scenario="Not found" {"message": "Project not found."}
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        return response()->noContent();
    }

    /**
     * Restore a soft-deleted project.
     *
     * Only admins can restore projects.
     *
     * @group Projects
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @response {
     *   "id": 1,
     *   "name": "FTTH Bordeaux Nord",
     *   "status": "draft",
     *   "created_at": "2026-01-15T10:30:00.000000Z",
     *   "updated_at": "2026-01-15T10:30:00.000000Z"
     * }
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     * @response 404 scenario="Not found" {"message": "Project not found."}
     */
    public function restore(Project $project)
    {
        $this->authorize('restore', $project);

        $this->projectService->restore($project);

        return new ProjectResource($project->fresh());
    }
}
