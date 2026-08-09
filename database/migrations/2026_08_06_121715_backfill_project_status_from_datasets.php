<?php

use App\Enums\ProjectStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('projects')
            ->where('status', ProjectStatus::Draft->value)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('project_datasets')
                    ->whereColumn('project_datasets.project_id', 'projects.id');
            })
            ->update(['status' => ProjectStatus::InProgress->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('projects')
            ->where('status', ProjectStatus::InProgress->value)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('project_datasets')
                    ->whereColumn('project_datasets.project_id', 'projects.id');
            })
            ->update(['status' => ProjectStatus::Draft->value]);
    }
};
