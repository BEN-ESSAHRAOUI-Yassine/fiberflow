Step 1 — Create the Docker Compose file
In the root of your Laravel project, create docker-compose.yml:
yamlservices:
postgis:
image: postgis/postgis:16-3.4
container_name: fiberflow_postgis
environment:
POSTGRES_DB: gracethd_db
POSTGRES_USER: gracethd_user
POSTGRES_PASSWORD: gracethd_pass
ports: - "5432:5432"
volumes: - postgis_data:/var/lib/postgresql/data
restart: unless-stopped

volumes:
postgis_data:

Step 2 — Start the container
Open a terminal in your Laravel project root:
bashdocker-compose up -d
Verify it's running:
bashdocker ps
You should see fiberflow_postgis with status Up.

Step 3 — Verify PostGIS is active
bashdocker exec -it fiberflow_postgis psql -U gracethd_user -d gracethd_db -c "SELECT PostGIS_Version();"
Expected output something like:
3.4.0

Step 4 — Connect QGIS to the container
Open QGIS → Browser Panel → right-click PostgreSQL → New Connection
Fill in:
FieldValueNameFiberFlow PostGISHostlocalhostPort5432Databasegracethd_dbUsernamegracethd_userPasswordgracethd_passSSL modedisable
Click Test Connection → should say "Connection to localhost was successful."
Click OK.

Step 5 — Configure Laravel .env
Add these lines to your .env:
env# MySQL — FiberFlow app database (default connection)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fiberflow
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

# PostGIS — read-only GIS data

POSTGIS_HOST=127.0.0.1
POSTGIS_PORT=5432
POSTGIS_DATABASE=gracethd_db
POSTGIS_USERNAME=gracethd_user
POSTGIS_PASSWORD=gracethd_pass

Step 6 — Configure Laravel database connections
In config/database.php, add the PostGIS connection inside the connections array:
php'postgis' => [
'driver' => 'pgsql',
'host' => env('POSTGIS_HOST', '127.0.0.1'),
'port' => env('POSTGIS_PORT', '5432'),
'database' => env('POSTGIS_DATABASE', 'gracethd_db'),
'username' => env('POSTGIS_USERNAME', 'gracethd_user'),
'password' => env('POSTGIS_PASSWORD', ''),
'charset' => 'utf8',
'prefix' => '',
'schema' => 'public',
'sslmode' => 'prefer',
],

Step 7 — Test the connection from Laravel
Run a quick test in php artisan tinker:
phpDB::connection('postgis')->select('SELECT PostGIS_Version() as version');
Expected:
=> [
{#version: "3.4.0 ..."}
]

Step 8 — Enable PHP PostgreSQL driver
Laravel needs ext-pdo_pgsql to talk to PostgreSQL. Check if it's enabled:
bashphp -m | grep pgsql
If nothing shows up, open your php.ini (find it with php --ini) and uncomment:
extension=pdo_pgsql
extension=pgsql
Then restart your PHP/web server.

Once all 8 steps work, tell me and we move directly to creating the GraceTHD tables inside the container and loading the ANFI-BAUGE01 data for the demo. Then migrations.

Step 9 — Create GraceTHD tables in PostGIS
Create a file called gracethd_schema.sql in your project root:
sql-- Connect to gracethd_db before running this

-- Enable PostGIS (already done at container creation, but safe to repeat)
CREATE EXTENSION IF NOT EXISTS postgis;

-- ===========================================
-- TOPOLOGY
-- ===========================================

CREATE TABLE IF NOT EXISTS t_noeud (
nd_code VARCHAR(254) PRIMARY KEY,
nd_nom VARCHAR(254),
nd_type VARCHAR(50),
geom GEOMETRY(Point, 2154)
);

-- ===========================================
-- EQUIPMENT
-- ===========================================

CREATE TABLE IF NOT EXISTS t_ptech (
pt_code VARCHAR(254) PRIMARY KEY,
pt_nd_code VARCHAR(254) REFERENCES t_noeud(nd_code),
pt_typephy VARCHAR(50),
pt_typelog VARCHAR(50),
pt_etat VARCHAR(50),
pt_avct VARCHAR(50),
pt_nature VARCHAR(50),
pt_prop VARCHAR(254),
pt_gest VARCHAR(254)
);

CREATE TABLE IF NOT EXISTS t_ebp (
bp_code VARCHAR(254) PRIMARY KEY,
bp_nd_code VARCHAR(254) REFERENCES t_noeud(nd_code),
bp_typephy VARCHAR(50),
bp_typelog VARCHAR(50),
bp_etat VARCHAR(50),
bp_prop VARCHAR(254)
);

CREATE TABLE IF NOT EXISTS t_sitetech (
st_code VARCHAR(254) PRIMARY KEY,
st_nd_code VARCHAR(254) REFERENCES t_noeud(nd_code),
st_typ VARCHAR(50),
st_etat VARCHAR(50),
st_prop VARCHAR(254)
);

-- ===========================================
-- CABLES
-- ===========================================

CREATE TABLE IF NOT EXISTS t_cable (
cb_code VARCHAR(254) PRIMARY KEY,
cb_fo INTEGER,
cb_typelog VARCHAR(50),
cb_etat VARCHAR(50),
cb_prop VARCHAR(254)
);

CREATE TABLE IF NOT EXISTS t_cableline (
cl_code VARCHAR(254) PRIMARY KEY,
cl_cb_code VARCHAR(254) REFERENCES t_cable(cb_code),
geom GEOMETRY(LineString, 2154)
);

-- ===========================================
-- INFRASTRUCTURE
-- ===========================================

CREATE TABLE IF NOT EXISTS t_cheminement (
ch_code VARCHAR(254) PRIMARY KEY,
ch_typ VARCHAR(50),
geom GEOMETRY(LineString, 2154)
);

CREATE TABLE IF NOT EXISTS t_conduite (
cd_code VARCHAR(254) PRIMARY KEY,
cd_typ VARCHAR(50),
cd_dia_int DECIMAL(6,2),
geom GEOMETRY(LineString, 2154)
);

-- ===========================================
-- ZONES
-- ===========================================

CREATE TABLE IF NOT EXISTS t_znro (
zn_code VARCHAR(254) PRIMARY KEY,
zn_nd_code VARCHAR(254),
geom GEOMETRY(MultiPolygon, 2154)
);

CREATE TABLE IF NOT EXISTS t_zsro (
zs_code VARCHAR(254) PRIMARY KEY,
zs_nd_code VARCHAR(254),
geom GEOMETRY(MultiPolygon, 2154)
);

CREATE TABLE IF NOT EXISTS t_zpbo (
zp_code VARCHAR(254) PRIMARY KEY,
zp_nd_code VARCHAR(254),
geom GEOMETRY(MultiPolygon, 2154)
);

-- ===========================================
-- SPATIAL INDEXES
-- ===========================================

CREATE INDEX IF NOT EXISTS idx_noeud_geom ON t_noeud USING GIST(geom);
CREATE INDEX IF NOT EXISTS idx_cableline_geom ON t_cableline USING GIST(geom);
CREATE INDEX IF NOT EXISTS idx_cheminement_geom ON t_cheminement USING GIST(geom);
CREATE INDEX IF NOT EXISTS idx_conduite_geom ON t_conduite USING GIST(geom);
CREATE INDEX IF NOT EXISTS idx_znro_geom ON t_znro USING GIST(geom);
CREATE INDEX IF NOT EXISTS idx_zsro_geom ON t_zsro USING GIST(geom);
CREATE INDEX IF NOT EXISTS idx_zpbo_geom ON t_zpbo USING GIST(geom);
Run it against the container:
bashdocker exec -i fiberflow_postgis psql -U gracethd_user -d gracethd_db < gracethd_schema.sql
Verify tables were created:
bashdocker exec -it fiberflow_postgis psql -U gracethd_user -d gracethd_db -c "\dt"
You should see 11 tables listed.

Step 10 — Load ANFI-BAUGE01 demo data
You have the real livrable data from the ANFI-BAUGE01 zip. You need ogr2ogr installed — it comes with GDAL. On Windows, the easiest way is via OSGeo4W Shell (already installed with QGIS).
Open OSGeo4W Shell (search for it in Start menu) and run:
bash# Set your PIVOT folder path
set PIVOT=C:\path\to\ANFI-BAUGE01_APD\PIVOT
set PG=host=127.0.0.1 dbname=gracethd_db user=gracethd_user password=gracethd_pass

# Import nodes

ogr2ogr -f "PostgreSQL" PG:"%PG%" "%PIVOT%\noeud.shp" -nln t_noeud -overwrite -t_srs EPSG:2154

# Import cables geometry

ogr2ogr -f "PostgreSQL" PG:"%PG%" "%PIVOT%\cable.shp" -nln t_cableline -overwrite -t_srs EPSG:2154

# Import cheminement

ogr2ogr -f "PostgreSQL" PG:"%PG%" "%PIVOT%\cheminement.shp" -nln t_cheminement -overwrite -t_srs EPSG:2154

# Import zones

ogr2ogr -f "PostgreSQL" PG:"%PG%" "%PIVOT%\zbdi.shp" -nln t_znro -overwrite -t_srs EPSG:2154
ogr2ogr -f "PostgreSQL" PG:"%PG%" "%PIVOT%\zsro.shp" -nln t_zsro -overwrite -t_srs EPSG:2154
ogr2ogr -f "PostgreSQL" PG:"%PG%" "%PIVOT%\zpbo.shp" -nln t_zpbo -overwrite -t_srs EPSG:2154
For the CSV files (no geometry):
bash# Inside the container, copy and import CSV
docker cp "%PIVOT%\cassette.csv" fiberflow_postgis:/tmp/cassette.csv

docker exec -it fiberflow_postgis psql -U gracethd_user -d gracethd_db \
 -c "\copy t_ebp FROM '/tmp/cassette.csv' CSV HEADER DELIMITER ';'"
Verify data loaded:
bashdocker exec -it fiberflow_postgis psql -U gracethd_user -d gracethd_db \
 -c "SELECT COUNT(\*) FROM t_noeud;"

Step 11 — Laravel migrations
Now we write the 6 MySQL migration files. Run these in your Laravel project:
bashphp artisan make:migration create_users_table
php artisan make:migration create_projects_table
php artisan make:migration create_project_datasets_table
php artisan make:migration create_audits_table
php artisan make:migration create_ai_conversations_table
php artisan make:migration create_ai_messages_table
Then fill each one:
create_projects_table:
phpSchema::create('projects', function (Blueprint $table) {
$table->id();
$table->foreignId('created_by')->constrained('users');
$table->foreignId('parent_project_id')->nullable()->constrained('projects');
$table->string('name', 150);
$table->text('description')->nullable();
$table->string('client', 100);
$table->string('municipality', 100);
$table->enum('project_type', ['transport', 'distribution']);
$table->enum('study_phase', ['APS', 'APD', 'PRO', 'EXE', 'DOE', 'FIN']);
$table->string('gis_project_id', 100); // znro_code
$table->enum('status', ['draft', 'in_progress', 'audited', 'validated', 'archived'])
->default('draft');
$table->timestamps();
$table->softDeletes();
});
create_project_datasets_table:
phpSchema::create('project_datasets', function (Blueprint $table) {
$table->id();
$table->foreignId('project_id')->constrained('projects');
$table->json('geojson');
$table->timestamp('imported_at');
$table->timestamps();
$table->softDeletes();
});
create_audits_table:
phpSchema::create('audits', function (Blueprint $table) {
$table->id();
$table->foreignId('project_id')->constrained('projects');
$table->foreignId('projectdataset_id')->constrained('project_datasets');
$table->foreignId('performed_by')->constrained('users');
$table->enum('project_type_at_audit', ['transport', 'distribution']);
$table->enum('phase_at_audit', ['APS', 'APD', 'PRO', 'EXE', 'DOE', 'FIN']);
$table->enum('status', ['pending', 'running', 'completed', 'failed'])
->default('pending');
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
create_ai_conversations_table:
phpSchema::create('ai_conversations', function (Blueprint $table) {
$table->id();
$table->foreignId('project_id')->constrained('projects');
$table->foreignId('audit_id')->nullable()->constrained('audits');
$table->foreignId('user_id')->constrained('users');
$table->timestamps();
});
create_ai_messages_table:
phpSchema::create('ai_messages', function (Blueprint $table) {
$table->id();
$table->foreignId('conversation_id')->constrained('ai_conversations');
$table->enum('role', ['user', 'assistant']);
$table->longText('message');
$table->timestamps();
});
Then run:
bashphp artisan migrate
