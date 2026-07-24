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

    public function index()
    {
        $this->authorize('viewAny', Project::class);

        return new ProjectCollection($this->projectService->list(auth()->user()));
    }

    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        $data = array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]);

        $project = $this->projectService->create($data);

        return new ProjectResource($project);
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return new ProjectResource($project->loadMissing(['creator', 'parentProject', 'childProjects']));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project = $this->projectService->update($project, $request->validated());

        return new ProjectResource($project);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        return response()->noContent();
    }

    public function restore(Project $project)
    {
        $this->authorize('restore', $project);

        $this->projectService->restore($project);

        return new ProjectResource($project->fresh());
    }
}
