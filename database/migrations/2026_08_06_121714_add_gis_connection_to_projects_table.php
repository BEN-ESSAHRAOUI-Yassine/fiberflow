<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('gis_host')->nullable()->after('status');
            $table->string('gis_port')->nullable()->after('gis_host');
            $table->string('gis_database')->nullable()->after('gis_port');
            $table->string('gis_schema')->nullable()->after('gis_database');
            $table->string('gis_username')->nullable()->after('gis_schema');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['gis_host', 'gis_port', 'gis_database', 'gis_schema', 'gis_username']);
        });
    }
};
