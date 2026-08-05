<?php

namespace App\Http\Controllers;

use App\Enums\AuditStatus;
use App\Exports\AuditExport;
use App\Jobs\RunAuditJob;
use App\Models\Audit;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AuditController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $this->authorize('viewAny', Audit::class);

        $audits = $project->audits()
            ->with('performer')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('performed_by', $request->user()->id))
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

    public function retry(Project $project, Audit $audit): RedirectResponse
    {
        $this->authorize('view', $audit);

        if (! $this->isRetryable($audit)) {
            return redirect()
                ->route('admin.projects.audits.show', [$project, $audit])
                ->with('error', __('Only failed or stalled audits can be retried.'));
        }

        $audit->update([
            'status' => AuditStatus::Pending,
            'error_message' => null,
        ]);

        RunAuditJob::dispatch($audit->id);

        return redirect()
            ->route('admin.projects.audits.show', [$project, $audit])
            ->with('success', __('Audit relaunched.'));
    }

    protected function isRetryable(Audit $audit): bool
    {
        if ($audit->status === AuditStatus::Failed) {
            return true;
        }

        return $audit->status === AuditStatus::Running
            && $audit->updated_at->lt(now()->subMinutes(30));
    }

    public function pdf(Project $project, Audit $audit)
    {
        $this->authorize('view', $audit);

        set_time_limit(60);

        $audit->loadMissing(['performer', 'dataset']);

        $filename = sprintf('audit-%d-%s-%s.pdf',
            $audit->id,
            $project->slug,
            now()->format('Y-m-d')
        );

        return Pdf::loadView('audits.pdf', compact('project', 'audit'))
            ->download($filename);
    }

    public function excel(Project $project, Audit $audit)
    {
        $this->authorize('view', $audit);

        $audit->loadMissing(['performer', 'dataset']);

        $filename = sprintf('audit-%d-%s-%s.xlsx',
            $audit->id,
            $project->slug,
            now()->format('Y-m-d')
        );

        return Excel::download(new AuditExport($audit), $filename);
    }
}
