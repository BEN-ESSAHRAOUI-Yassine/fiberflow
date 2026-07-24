<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GISService
{
    private const SCHEMAS = [
        'apd_07' => 'GRACETHD_APD_NRO71153CRI_07_D',
        'apd_08' => 'GRACETHD_APD_NRO71153CRI_08_D',
        'rec_08' => 'GRACETHD_REC_NRO71153CRI_08_D',
    ];

    private const TABLES = [
        't_noeud' => 'geom',
        't_ptech' => null,
        't_ebp' => null,
        't_sitetech' => null,
        't_cable' => null,
        't_cableline' => 'geom',
        't_cheminement' => 'geom',
        't_conduite' => null,
        't_znro' => 'geom',
        't_zsro' => 'geom',
        't_zpbo' => 'geom',
    ];

    public function importFromPostGIS(string $schema): array
    {
        $geojson = [];
        $counts = [];

        foreach (self::TABLES as $table => $geomColumn) {
            $features = $this->queryTable($schema, $table, $geomColumn);

            $geojson[$table] = $features;
            $counts[$table] = count($features);
        }

        return [
            'geojson' => $geojson,
            'counts' => $counts,
        ];
    }

    public function getAvailableSchemas(): Collection
    {
        if (! $this->isPostGIS()) {
            return collect();
        }

        $existing = DB::connection('postgis')
            ->table('information_schema.schemata')
            ->whereIn('schema_name', array_keys(self::SCHEMAS))
            ->pluck('schema_name')
            ->toArray();

        return collect(self::SCHEMAS)
            ->filter(fn ($label, $name) => in_array($name, $existing, true))
            ->map(fn ($label, $name) => (object) ['schema' => $name, 'label' => $label])
            ->values();
    }

    public function isPostGIS(): bool
    {
        return DB::connection('postgis')->getDriverName() === 'pgsql';
    }

    private function queryTable(string $schema, string $table, ?string $geomColumn): array
    {
        try {
            $qualifiedTable = $this->isPostGIS()
                ? "{$schema}.{$table}"
                : $table;

            $query = DB::connection('postgis')->table($qualifiedTable)->select('*');

            if ($geomColumn) {
                $query->addSelect(DB::raw("ST_AsGeoJSON(ST_Transform({$geomColumn}, 4326)) AS {$geomColumn}_json"));
            }

            $rows = $query->get();

            return $rows->map(function ($row) use ($geomColumn) {
                $rowArray = (array) $row;

                $feature = [
                    'type' => 'Feature',
                    'geometry' => null,
                    'properties' => [],
                ];

                $geomJsonKey = $geomColumn ? "{$geomColumn}_json" : null;

                foreach ($rowArray as $key => $value) {
                    if ($geomJsonKey && $key === $geomJsonKey) {
                        if (is_string($value)) {
                            $decoded = json_decode($value, true);
                            $feature['geometry'] = $decoded ?? null;
                        } elseif (is_object($value) || is_array($value)) {
                            $feature['geometry'] = json_decode(json_encode($value), true);
                        }
                    } elseif ($geomColumn && $key === $geomColumn) {
                        continue;
                    } else {
                        $feature['properties'][$key] = $value;
                    }
                }

                return $feature;
            })->toArray();
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to query PostGIS table '{$schema}.{$table}': ".$e->getMessage());
        }
    }
}
