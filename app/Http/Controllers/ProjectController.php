<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(protected ProjectService $projectService) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $filters = $request->only(['archived', 'search', 'project_type', 'client', 'status', 'study_phase', 'sort', 'direction']);
        $projects = $this->projectService->list(auth()->user(), $filters);

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        $transportProjects = Project::where('project_type', 'transport')->get();

        return view('projects.create', compact('transportProjects'));
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $data = array_merge($request->validated(), [
            'created_by' => auth()->id(),
        ]);

        $this->projectService->create($data);

        return redirect()->route('admin.projects.index')->with('success', __('Project created.'));
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load(['childProjects', 'datasets', 'audits' => function ($query) {
            $query->latest()->limit(5);
        }]);

        $datasetsCount = $project->datasets()->count();
        $auditsCount = $project->audits()->count();
        $latestDataset = $project->datasets()->latest()->first();
        $featuresCount = $latestDataset ? collect($latestDataset->geojson)->flatten()->count() : 0;

        return view('projects.show', compact('project', 'datasetsCount', 'auditsCount', 'featuresCount'));
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        $transportProjects = Project::where('project_type', 'transport')
            ->where('id', '!=', $project->id)
            ->get();

        return view('projects.edit', compact('project', 'transportProjects'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $this->projectService->update($project, $request->validated());

        return redirect()->route('admin.projects.index')->with('success', __('Project updated.'));
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $this->projectService->delete($project);

        return redirect()->route('admin.projects.index')->with('success', __('Project archived.'));
    }

    public function restore(Project $project): RedirectResponse
    {
        $this->authorize('restore', $project);

        $this->projectService->restore($project);

        return redirect()->route('admin.projects.index')->with('success', __('Project restored.'));
    }
}
