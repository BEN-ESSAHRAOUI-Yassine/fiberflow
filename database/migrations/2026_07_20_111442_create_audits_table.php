<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('projectdataset_id')->constrained('project_datasets');
            $table->foreignId('performed_by')->constrained('users');
            $table->string('project_type_at_audit');
            $table->string('phase_at_audit');
            $table->string('status')->default('pending');
            $table->decimal('quality_score', 5, 2)->nullable();
            $table->decimal('connectivity_score', 5, 2)->nullable();
            $table->decimal('coherence_score', 5, 2)->nullable();
            $table->decimal('capacity_score', 5, 2)->nullable();
            $table->decimal('extensibility_score', 5, 2)->nullable();
            $table->json('network_statistics')->nullable();
            $table->longText('ai_summary')->nullable();
            $table->longText('recommendations')->nullable();
            $table->integer('anomaly_count')->default(0);
            $table->integer('critical_anomaly_count')->default(0);
            $table->string('model_used', 100)->nullable();
            $table->integer('tokens_used')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
