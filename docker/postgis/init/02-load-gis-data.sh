#!/bin/bash
set -e

PG_USER="fiberflow"
PG_DB="fiberflow_gis"
PGDATA="/var/lib/postgresql/data"
DATA_ROOT="/data/gis"

FOLDERS=(
    "GRACETHD_APD_NRO71153CRI_07_D:apd_07"
    "GRACETHD_APD_NRO71153CRI_08_D:apd_08"
    "GRACETHD_REC_NRO71153CRI_08_D:rec_08"
)

echo "================================================================"
echo "  Restarting PostgreSQL with TCP for ogr2ogr..."
echo "================================================================"

# Stop the temp server started by entrypoint (Unix socket only)
pg_ctl -D "$PGDATA" -m fast -w stop 2>/dev/null || echo "  (stop handled)"

# Start with TCP enabled
pg_ctl -D "$PGDATA" -o "-c listen_addresses='*' -c port=5432" -w start 2>/dev/null

# Wait for TCP
for i in $(seq 1 15); do
    if pg_isready -h localhost -p 5432 -U "$PG_USER" -d "$PG_DB" 2>/dev/null; then
        echo "  PostgreSQL ready on TCP"
        break
    fi
    sleep 1
done

echo ""
echo "================================================================"
echo "  Loading GIS data via ogr2ogr..."
echo "================================================================"

for entry in "${FOLDERS[@]}"; do
    folder="${entry%%:*}"
    schema="${entry##*:}"
    dir="$DATA_ROOT/$folder"

    echo ""
    echo "=== $folder → schema: $schema ==="

    psql -U "$PG_USER" -d "$PG_DB" -c "CREATE SCHEMA IF NOT EXISTS $schema;"

    OGR_CONN="PG:dbname='$PG_DB' user='$PG_USER' host='localhost' port='5432'"

    # Shapefiles
    for shp in "$dir"/*.shp; do
        [ -f "$shp" ] || continue
        table=$(basename "$shp" .shp)
        echo "  -> $table (shapefile)"
        ogr2ogr -f PostgreSQL \
            -lco GEOMETRY_NAME=geom \
            -lco SCHEMA="$schema" \
            -lco DIM=2 \
            -nln "$table" \
            -nlt PROMOTE_TO_MULTI \
            -overwrite \
            "$OGR_CONN" "$shp" 2>/dev/null || echo "     (failed)"
    done

    # CSV files
    for csv in "$dir"/*.csv; do
        [ -f "$csv" ] || continue
        table=$(basename "$csv" .csv)
        line_count=$(wc -l < "$csv" | tr -d ' ')
        [ "$line_count" -le 1 ] && continue

        echo "  -> $table (CSV, $line_count lines)"

        header=$(head -1 "$csv" | sed 's/\r//g')
        columns=$(echo "$header" | sed 's/;/ TEXT, /g')

        psql -U "$PG_USER" -d "$PG_DB" -c "CREATE TABLE IF NOT EXISTS $schema.$table ($columns TEXT);"
        psql -U "$PG_USER" -d "$PG_DB" -c "\copy $schema.$table FROM '$csv' DELIMITER ';' CSV HEADER" || echo "     (copy failed)"
    done

    # Spatial indexes
    for tbl in t_noeud t_adresse t_cableline t_cheminement t_znro t_zpbo t_zsro; do
        psql -U "$PG_USER" -d "$PG_DB" -c "CREATE INDEX IF NOT EXISTS idx_${schema}_${tbl}_geom ON $schema.$tbl USING GIST (geom);" 2>/dev/null || true
    done
done

echo ""
echo "================================================================"
echo "  Creating public views..."
echo "================================================================"

for SCHEMA in rec_08 apd_07 apd_08; do
    TABLES=$(psql -U "$PG_USER" -d "$PG_DB" -t -A -c "
        SELECT table_name FROM information_schema.tables
        WHERE table_schema = '$SCHEMA' AND table_type = 'BASE TABLE'
        ORDER BY table_name;
    " 2>/dev/null)

    [ -z "$TABLES" ] && continue

    for TABLE in $TABLES; do
        psql -U "$PG_USER" -d "$PG_DB" -c "DROP TABLE IF EXISTS public.$TABLE CASCADE;" 2>/dev/null
        psql -U "$PG_USER" -d "$PG_DB" -c "CREATE VIEW public.$TABLE AS SELECT * FROM $SCHEMA.$TABLE;" 2>/dev/null || true
    done
done

echo ""
echo "================================================================"
echo "  Verification"
echo "================================================================"

for TABLE in t_noeud t_cableline t_cheminement t_znro t_zsro t_zpbo t_ptech t_cable t_conduite t_ebp t_sitetech; do
    COUNT=$(psql -U "$PG_USER" -d "$PG_DB" -t -A -c "SELECT count(*) FROM public.$TABLE;" 2>/dev/null | xargs)
    echo "  $TABLE: ${COUNT:-0} rows"
done

echo ""
echo "================================================================"
echo "  Stopping TCP server (entrypoint will restart normally)..."
echo "================================================================"

pg_ctl -D "$PGDATA" -m fast -w stop 2>/dev/null || echo "  (stop handled by entrypoint)"
