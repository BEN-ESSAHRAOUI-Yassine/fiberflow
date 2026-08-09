<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class GISService
{
    private const CONNECTION_NAME = 'ffgis';

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
        't_adresse' => 'geom',
    ];

    public function testConnection(array $connection): bool
    {
        try {
            $this->run($connection, fn () => DB::connection(self::CONNECTION_NAME)->select('SELECT 1'));

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function getAvailableSchemas(array $connection): Collection
    {
        try {
            return $this->run($connection, function () {
                $schemas = DB::connection(self::CONNECTION_NAME)
                    ->table('information_schema.schemata')
                    ->whereNotIn('schema_name', ['pg_catalog', 'information_schema', 'pg_toast', 'pg_temp_1', 'pg_toast_temp_1'])
                    ->where('schema_name', 'not like', 'pg\_%')
                    ->where('schema_name', 'not like', 'pg_temp\_%')
                    ->pluck('schema_name')
                    ->toArray();

                $candidates = collect($schemas)->filter(function (string $schema) {
                    return $this->schemaHoldsExpectedTables($schema);
                });

                return $candidates
                    ->map(fn (string $schema) => (object) ['schema' => $schema, 'label' => $schema])
                    ->values();
            });
        } catch (\Exception) {
            return collect();
        }
    }

    public function importFromPostGIS(array $connection, string $schema): array
    {
        return $this->run($connection, function () use ($schema) {
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
        });
    }

    private function schemaHoldsExpectedTables(string $schema): bool
    {
        $existing = DB::connection(self::CONNECTION_NAME)
            ->table('information_schema.tables')
            ->where('table_schema', $schema)
            ->whereIn('table_name', array_keys(self::TABLES))
            ->pluck('table_name');

        return $existing->intersect(array_keys(self::TABLES))->isNotEmpty();
    }

    private function queryTable(string $schema, string $table, ?string $geomColumn): array
    {
        try {
            $qualifiedTable = DB::connection(self::CONNECTION_NAME)->getDriverName() === 'pgsql'
                ? "{$schema}.{$table}"
                : $table;

            $query = DB::connection(self::CONNECTION_NAME)->table($qualifiedTable)->select('*');

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

    private function run(array $connection, callable $callback): mixed
    {
        $config = array_merge(
            $this->connectionConfig($connection),
            config('database.connections.'.self::CONNECTION_NAME, []),
        );

        Config::set('database.connections.'.self::CONNECTION_NAME, $config);
        DB::purge(self::CONNECTION_NAME);

        try {
            return $callback();
        } finally {
            DB::purge(self::CONNECTION_NAME);
        }
    }

    private function connectionConfig(array $connection): array
    {
        return [
            'driver' => 'pgsql',
            'host' => $connection['host'] ?? '127.0.0.1',
            'port' => $connection['port'] ?? '5432',
            'database' => $connection['database'] ?? '',
            'username' => $connection['username'] ?? '',
            'password' => $connection['password'] ?? '',
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ];
    }
}
