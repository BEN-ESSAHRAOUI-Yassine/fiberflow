<?php

namespace App\Http\Controllers;

use App\Enums\AuditStatus;
use App\Jobs\RunAuditJob;
use App\Models\Audit;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $this->authorize('viewAny', Audit::class);

        $audits = $project->audits()
            ->with('performer')
            ->orderByDesc('created_at')
            ->paginate();

        return view('audits.index', compact('project', 'audits'));
    }

    public function store(Project $project): RedirectResponse
    {
        $this->authorize('create', Audit::class);

        $dataset = $project->datasets()->latest()->first();

        if (! $dataset) {
            return redirect()
                ->route('admin.projects.show', $project)
                ->with('error', __('Project has no dataset. Import a dataset before running an audit.'));
        }

        $audit = Audit::create([
            'project_id' => $project->id,
            'projectdataset_id' => $dataset->id,
            'performed_by' => auth()->id(),
            'project_type_at_audit' => $project->project_type->value,
            'phase_at_audit' => $project->study_phase->value,
            'status' => AuditStatus::Pending,
        ]);

        RunAuditJob::dispatch($audit->id);

        return redirect()
            ->route('admin.projects.audits.show', [$project, $audit])
            ->with('success', __('Audit launched.'));
    }

    public function show(Project $project, Audit $audit): View
    {
        $this->authorize('view', $audit);

        $audit->loadMissing(['performer', 'dataset']);

        return view('audits.show', compact('project', 'audit'));
    }
}
