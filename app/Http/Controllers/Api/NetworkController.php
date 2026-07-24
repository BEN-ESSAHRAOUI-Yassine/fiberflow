<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index(Request $request, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $latestDataset = $project->datasets()->latest('imported_at')->first();

        if (! $latestDataset) {
            return response()->json([
                'data' => null,
                'message' => __('No dataset imported for this project.'),
            ]);
        }

        $geojson = $latestDataset->geojson;

        if ($request->filled('layer')) {
            $layer = $request->input('layer');

            if (isset($geojson[$layer])) {
                $features = $geojson[$layer];
            } else {
                $features = [];
            }
        } else {
            $allFeatures = [];

            foreach ($geojson as $table => $tableFeatures) {
                foreach ($tableFeatures as $feature) {
                    $allFeatures[] = $feature;
                }
            }

            $features = $allFeatures;
        }

        if ($request->filled('search')) {
            $search = mb_strtolower($request->input('search'));

            $features = array_values(array_filter($features, function ($feature) use ($search) {
                if (! isset($feature['properties'])) {
                    return false;
                }

                foreach ($feature['properties'] as $value) {
                    if ($value !== null && mb_strpos(mb_strtolower((string) $value), $search) !== false) {
                        return true;
                    }
                }

                return false;
            }));
        }

        return response()->json([
            'data' => [
                'type' => 'FeatureCollection',
                'features' => $features,
            ],
            'count' => count($features),
            'layer' => $request->input('layer'),
        ]);
    }
}
