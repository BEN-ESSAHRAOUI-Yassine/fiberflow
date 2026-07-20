<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('parent_project_id')->nullable()->constrained('projects');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('client', 100);
            $table->string('municipality', 100);
            $table->string('project_type');
            $table->string('study_phase');
            $table->string('gis_project_id', 100);
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
