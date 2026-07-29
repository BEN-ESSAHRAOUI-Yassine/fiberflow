<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    /**
     * Get network GeoJSON features for a project.
     *
     * @group Network
     *
     * Returns the latest dataset's network features as a GeoJSON FeatureCollection.
     * Optionally filter by layer and/or search by property values.
     *
     * @urlParam project integer required The project ID. Example: 1
     *
     * @queryParam layer string Optional layer/table name to filter by. Example: fiber_segments
     * @queryParam search string Optional search term to filter features by property values. Example: Aerial
     *
     * @response {
     *   "data": {
     *     "type": "FeatureCollection",
     *     "features": [
     *       {
     *         "type": "Feature",
     *         "geometry": {"type": "LineString", "coordinates": [[-0.5793, 44.8378], [-0.5801, 44.8385]]},
     *         "properties": {"id": 1, "type": "Aerial", "length_m": 150.5}
     *       }
     *     ]
     *   },
     *   "count": 1,
     *   "layer": "fiber_segments"
     * }
     * @response {
     *   "data": null,
     *   "message": "No dataset imported for this project."
     * }
     * @response 403 scenario="Unauthorized" {"message": "This action is unauthorized."}
     */
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
