#!/bin/bash
set -e

export PGPASSWORD=fiberflow
PG_USER="fiberflow"
PG_DB="fiberflow_gis"

PSQL_CMD="psql -U $PG_USER -d $PG_DB"
OGR_CONN="PG:dbname='$PG_DB' user='$PG_USER' host='localhost' port='5432'"
DATA_ROOT="/data/gis"

FOLDERS=(
    "GRACETHD_APD_NRO71153CRI_07_D:apd_07"
    "GRACETHD_APD_NRO71153CRI_08_D:apd_08"
    "GRACETHD_REC_NRO71153CRI_08_D:rec_08"
)

create_table_from_header() {
    local csv="$1"
    local schema="$2"
    local table="$3"
    local header

    header=$(head -1 "$csv")
    header=$(echo "$header" | sed 's/\r//g')
    columns=$(echo "$header" | sed 's/;/ TEXT, /g')

    $PSQL_CMD -c "CREATE TABLE IF NOT EXISTS $schema.$table ($columns TEXT);"
}

import_shapefile() {
    local shp="$1"
    local schema="$2"
    local table="$3"

    echo "  -> $table (shapefile)"
    ogr2ogr -f PostgreSQL \
        -lco GEOMETRY_NAME=geom \
        -lco SCHEMA="$schema" \
        -lco DIM=2 \
        -nln "$table" \
        -nlt PROMOTE_TO_MULTI \
        -overwrite \
        "$OGR_CONN" "$shp"
}

import_dbf() {
    local dbf="$1"
    local schema="$2"
    local table="$3"

    echo "  -> $table (DBF)"
    ogr2ogr -f PostgreSQL \
        -lco SCHEMA="$schema" \
        -lco DIM=2 \
        -nln "$table" \
        -overwrite \
        "$OGR_CONN" "$dbf" 2>/dev/null || \
    echo "     (DBF import skipped or failed)"
}

import_csv() {
    local csv="$1"
    local schema="$2"
    local table="$3"
    local line_count

    line_count=$(wc -l < "$csv" | tr -d ' ')

    if [ "$line_count" -le 1 ]; then
        echo "  -> $table (CSV, empty - creating from header)"
        create_table_from_header "$csv" "$schema" "$table"
    else
        echo "  -> $table (CSV, $line_count lines)"
        ogr2ogr -f PostgreSQL \
            -lco SCHEMA="$schema" \
            -lco DIM=2 \
            -nln "$table" \
            -oo AUTODETECT_TYPE=YES \
            -oo SEPARATOR=SEMICOLON \
            -overwrite \
            "$OGR_CONN" "$csv" 2>/dev/null || {
            echo "     (ogr2ogr failed, using header + psql \copy)"
            create_table_from_header "$csv" "$schema" "$table"
            $PSQL_CMD -c "\copy $schema.$table FROM '$csv' DELIMITER ';' CSV HEADER"
        }
    fi
}

# Main loop
for entry in "${FOLDERS[@]}"; do
    folder="${entry%%:*}"
    schema="${entry##*:}"
    dir="$DATA_ROOT/$folder"

    echo ""
    echo "========================================================================"
    echo "  $folder → schema: $schema"
    echo "========================================================================"

    $PSQL_CMD -c "CREATE SCHEMA IF NOT EXISTS $schema;"

    # -- Shapefiles --
    echo ""
    echo "--- Shapefiles ---"
    for shp in "$dir"/*.shp; do
        [ -f "$shp" ] || continue
        table=$(basename "$shp" .shp)
        import_shapefile "$shp" "$schema" "$table"
    done

    # -- Attribute-only DBF --
    echo ""
    echo "--- Attribute-only DBF ---"
    for dbf in "$dir"/*.dbf; do
        [ -f "$dbf" ] || continue
        base=$(basename "$dbf" .dbf)
        shp_file="$dir/$base.shp"
        [ -f "$shp_file" ] && continue
        import_dbf "$dbf" "$schema" "$base"
    done

    # -- CSV files --
    echo ""
    echo "--- CSV files ---"
    for csv in "$dir"/*.csv; do
        [ -f "$csv" ] || continue
        table=$(basename "$csv" .csv)
        import_csv "$csv" "$schema" "$table"
    done

    # -- Spatial indexes --
    echo ""
    echo "--- Spatial indexes ---"
    for tbl in t_noeud t_adresse t_cableline t_cheminement t_znro t_zpbo t_zsro; do
        $PSQL_CMD -c "CREATE INDEX IF NOT EXISTS idx_${schema}_${tbl}_geom ON $schema.$tbl USING GIST (geom);" 2>/dev/null || true
    done

    # -- Views --
    echo ""
    echo "--- Views ---"

    # v_cable: cables with spatial route
    $PSQL_CMD -c "
        CREATE OR REPLACE VIEW $schema.v_cable AS
        SELECT c.*, cl.geom
        FROM $schema.t_cable c
        LEFT JOIN $schema.t_cableline cl ON c.cb_code = cl.cl_cb_code;
    " 2>/dev/null && echo "  -> v_cable"

    # v_ptech: technical points with node geometry
    $PSQL_CMD -c "
        CREATE OR REPLACE VIEW $schema.v_ptech AS
        SELECT p.*, n.geom
        FROM $schema.t_ptech p
        LEFT JOIN $schema.t_noeud n ON p.pt_nd_code = n.nd_code;
    " 2>/dev/null && echo "  -> v_ptech"

    # v_ebp: breakout points with location
    $PSQL_CMD -c "
        CREATE OR REPLACE VIEW $schema.v_ebp AS
        SELECT e.*, n.geom
        FROM $schema.t_ebp e
        LEFT JOIN $schema.t_ptech p ON e.bp_pt_code = p.pt_code
        LEFT JOIN $schema.t_noeud n ON p.pt_nd_code = n.nd_code;
    " 2>/dev/null && echo "  -> v_ebp"

    # v_suf: subscribers with address location
    $PSQL_CMD -c "
        CREATE OR REPLACE VIEW $schema.v_suf AS
        SELECT s.*, a.geom
        FROM $schema.t_suf s
        LEFT JOIN $schema.t_adresse a ON s.sf_ad_code = a.ad_code;
    " 2>/dev/null && echo "  -> v_suf"

    # v_conduite: conduits with pathway route
    $PSQL_CMD -c "
        CREATE OR REPLACE VIEW $schema.v_conduite AS
        SELECT cd.*, ch.geom
        FROM $schema.t_conduite cd
        LEFT JOIN $schema.t_cond_chem cc ON cd.cd_code = cc.dm_cd_code
        LEFT JOIN $schema.t_cheminement ch ON cc.dm_cm_code = ch.cm_code;
    " 2>/dev/null && echo "  -> v_conduite"

    # v_noeud_detail: nodes enriched with ptech attributes
    $PSQL_CMD -c "
        CREATE OR REPLACE VIEW $schema.v_noeud_detail AS
        SELECT n.*, p.pt_code, p.pt_etiquet, p.pt_typephy, p.pt_nature, p.pt_occp
        FROM $schema.t_noeud n
        LEFT JOIN $schema.t_ptech p ON n.nd_code = p.pt_nd_code;
    " 2>/dev/null && echo "  -> v_noeud_detail"
done

# -- Verification --
echo ""
echo "========================================================================"
echo "  Verification"
echo "========================================================================"
for entry in "${FOLDERS[@]}"; do
    schema="${entry##*:}"

    echo ""
    echo "--- Schema: $schema ---"
    echo ""

    $PSQL_CMD -c "
        SELECT table_name,
               (xpath('/row/c/text()', query_to_xml(format('SELECT count(*) AS c FROM %I.%I', table_schema, table_name), true, false, '')))[1]::text::int AS count
        FROM information_schema.tables
        WHERE table_schema = '$schema'
          AND table_type = 'BASE TABLE'
        ORDER BY table_name;
    "
done

echo ""
echo "Done."
