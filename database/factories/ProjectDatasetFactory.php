<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectDatasetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'geojson' => [
                'type' => 'FeatureCollection',
                'features' => [],
                't_noeud' => [],
                't_ptech' => [],
                't_cable' => [],
            ],
            'imported_at' => now(),
        ];
    }
}
