# FiberFlow — Project Context Document
> Version 2.0 — Updated after OpenSpec review and all architecture decisions

---

## 1. PROJECT OVERVIEW

**Name:** FiberFlow
**Type:** Fil Rouge (academic capstone project)
**Author:** Yassine BEN ESSAHRAOUI
**Framework:** Laravel 13
**Language:** PHP 8.3
**Architecture:** API First
**AI Provider:** Groq (Laravel AI SDK)

**Purpose:** A Laravel API-first platform providing business intelligence, audit, and AI-assisted interpretation on top of GIS data managed by QGIS/PostGIS. FiberFlow does not edit spatial data — it imports, analyzes, audits, and exports it.

**Target users:**
- Ingénieur FTTH — imports datasets, launches audits, uses AI assistant, downloads reports
- Administrateur — manages users, manages projects, supervises platform

---

## 2. CORE CONCEPT

FTTH network studies are managed by two separate systems:

| System | Role | Technology |
|---|---|---|
| QGIS + PostGIS | Spatial data editing (terrain truth) | PostgreSQL + PostGIS extension |
| FiberFlow | Business logic, audit, AI, export | Laravel 13 + MySQL |

**Data flow:**

```
PostGIS (Source of Truth)
        │
   Import (GISService)
        │
        ▼
project_datasets.geojson (Working Copy — MySQL)
        │
        ├── AuditService → audits table
        │
        ├── AIService → ai_conversations / ai_messages
        │
        └── ReportService → PDF / Excel download
```

**Key principle:** After import, the audit engine never touches PostGIS again. The `project_datasets` table acts as a decoupled working copy. If PostGIS goes offline, all existing audits, reports, and AI conversations remain fully accessible.

---

## 3. FTTH DOMAIN KNOWLEDGE

### 3.1 Network Hierarchy

**Transport network:**
```
NRO (Nœud de Raccordement Optique)
  └── SRO (Sous-Répartiteur Optique)
```

**Distribution network:**
```
SRO (Sous-Répartiteur Optique)
  └── BO (Boîtier Optique)
       └── PBO (Point de Branchement Optique)
```

### 3.2 Project Types

| Type | Covers | Cable capacity | Has PBO? | Synoptique |
|---|---|---|---|---|
| Transport | NRO → SRO | High (288FO typical) | No | NRO → SRO |
| Distribution | SRO → BO → PBO | 12FO to 288FO | Yes | SRO → BO → PBO |

- A distribution project must always belong to a parent transport project (via `parent_project_id`)
- One project cannot be both types
- Project type is immutable after creation

### 3.3 Project Phases

Both project types follow the same phase lifecycle:

| Phase | Name |
|---|---|
| APS | Avant-Projet Sommaire |
| APD | Avant-Projet Détaillé |
| PRO | Projet |
| EXE | Exécution |
| DOE | Dossier des Ouvrages Exécutés |
| FIN | Fin / Réception |

The phase influences audit rules — anomalies acceptable at APS may be critical at EXE.

### 3.4 Route Types

| Route type | Contains |
|---|---|
| Optique | NRO, SRO, BO, PBO — optical equipment only |
| Infrastructure | Chambre (manhole), Poteau (pole), Conduite, Cheminement |

### 3.5 GraceTHD Reference

FiberFlow's PostGIS data model follows **GraceTHD**, the French national FTTH data exchange standard. PostGIS tables use GraceTHD naming conventions (`t_noeud`, `t_cable`, `t_ptech`, `t_cheminement`, etc.).

The `gis_project_id` column on projects stores a `znro_code` value (GraceTHD zone identifier) used to scope all PostGIS queries to the correct network zone during import.

---

## 4. ARCHITECTURE

### 4.1 Stack

| Layer | Technology |
|---|---|
| Backend framework | Laravel 13 |
| App database | MySQL |
| GIS database (read-only) | PostgreSQL + PostGIS (Docker container) |
| Frontend | Blade + TailwindCSS + AlpineJS |
| Map visualization | Leaflet (read-only, renders imported GeoJSON) |
| Charts | Chart.js (dashboard) |
| AI SDK | `laravel/ai` (official Laravel 13 package) |
| AI provider | Groq |
| PDF export | `barryvdh/laravel-dompdf` |
| Excel export | `maatwebsite/laravel-excel` |
| Queues | Laravel Queue + database driver |
| Auth | Laravel Breeze + Laravel Sanctum |
| API docs | Laravel Scribe |
| Testing | Pest |
| Containerization | Docker + Docker Compose |
| CI | GitHub Actions |
| Deployment | Azure (Ubuntu + Nginx + Supervisor) |

### 4.2 Database Connections

```php
// config/database.php
'connections' => [
    'mysql' => [
        // FiberFlow business data — default connection
        'driver' => 'mysql',
        'host'   => env('DB_HOST'),
        'database' => env('DB_DATABASE'),
        ...
    ],
    'postgis' => [
        // QGIS/PostGIS spatial data — read-only
        'driver'   => 'pgsql',
        'host'     => env('POSTGIS_HOST', '127.0.0.1'),
        'port'     => env('POSTGIS_PORT', '5432'),
        'database' => env('POSTGIS_DATABASE', 'gracethd_db'),
        'username' => env('POSTGIS_USERNAME', 'gracethd_user'),
        'password' => env('POSTGIS_PASSWORD', ''),
        'charset'  => 'utf8',
        'schema'   => 'public',
        'sslmode'  => 'prefer',
    ],
]
```

### 4.3 Architecture Style

```
Browser / API Client
        │
        ├── Blade Interface (Web Controllers)
        │
        └── REST API (API Controllers)
                │
        ────────────────────
               Services
        ────────────────────
                │
           Eloquent Models
                │
              MySQL
                │
     (import only) PostGIS
```

### 4.4 User Roles

| Role | Permissions |
|---|---|
| `admin` | Manage users, manage all projects, launch audits, view reports, dashboard |
| `ingenieur` | View projects, import dataset, launch audits, use AI, view reports, dashboard |

### 4.5 Background Processing

```
Controller
    │
Dispatch Job
    │
Queue (database driver)
    │
Supervisor (production)
    │
Job Execution
```

Jobs:
- `RunAuditJob` — executes technical audit, computes score
- `AnalyzeAuditJob` — sends results to Groq, stores AI summary
- `GenerateReportJob` — generates PDF/Excel

---

## 5. DATABASE SCHEMA

### 5.1 MySQL — FiberFlow application tables

---

#### users
```
id                  BIGINT PK AUTO_INCREMENT
name                VARCHAR(100) NOT NULL
email               VARCHAR(150) NOT NULL UNIQUE
password            VARCHAR(255) NOT NULL
role                ENUM('admin','ingenieur') DEFAULT 'ingenieur'
email_verified_at   TIMESTAMP NULL
remember_token      VARCHAR(100) NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

#### projects
```
id                  BIGINT PK AUTO_INCREMENT
created_by          FK → users.id
parent_project_id   FK → projects.id NULL
                    NULL for transport projects
                    Required for distribution projects
                    Must reference a project with project_type = 'transport'
name                VARCHAR(150) NOT NULL
description         TEXT NULL
client              VARCHAR(100) NOT NULL
municipality        VARCHAR(100) NOT NULL
project_type        ENUM('transport','distribution') NOT NULL
                    Immutable after creation
study_phase         ENUM('APS','APD','PRO','EXE','DOE','FIN') NOT NULL
gis_project_id      VARCHAR(100) NOT NULL
                    Stores znro_code from GraceTHD
                    Used to scope PostGIS queries during import
status              ENUM('draft','in_progress','audited','validated','archived')
                    DEFAULT 'draft'
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL (soft delete — archive only, no hard delete)
```

**Business rules:**
- `parent_project_id` MUST be NULL when `project_type = 'transport'`
- `parent_project_id` MUST be filled when `project_type = 'distribution'`
- Referenced `parent_project_id` must point to a `project_type = 'transport'` project
- `project_type` cannot be changed after creation
- Projects are archived via soft delete, never hard deleted

---

#### project_datasets
```
id                  BIGINT PK AUTO_INCREMENT
project_id          FK → projects.id
geojson             JSON NOT NULL
                    Full GeoJSON imported from PostGIS
                    Scoped by project.gis_project_id (znro_code)
                    Contains all GraceTHD network elements for the zone
imported_at         TIMESTAMP NOT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
deleted_at          TIMESTAMP NULL
                    Soft delete — old datasets kept when re-import occurs
                    Preserves audit history integrity
```

**GeoJSON structure (GraceTHD keys):**
```json
{
  "t_noeud": [],
  "t_ptech": [],
  "t_cable": [],
  "t_cableline": [],
  "t_cheminement": [],
  "t_conduite": [],
  "t_ebp": [],
  "t_sitetech": [],
  "t_znro": [],
  "t_zsro": [],
  "t_zpbo": []
}
```

**Import flow:**
1. Engineer clicks "Import Dataset" on a project
2. `GISService` reads PostGIS using `gis_project_id` (znro_code) as filter
3. Converts all results to GeoJSON via `ST_AsGeoJSON()`
4. Soft-deletes previous dataset (if any)
5. Creates new `project_datasets` record
6. Returns element count summary

**Re-import:**
- Previous dataset receives `deleted_at` timestamp
- New dataset is created fresh
- Old audits still reference their original dataset via `projectdataset_id`
- History fully preserved

---

#### audits
```
id                      BIGINT PK AUTO_INCREMENT
project_id              FK → projects.id
projectdataset_id       FK → project_datasets.id
                        References dataset used at audit time
                        Preserved even if dataset is later replaced
performed_by            FK → users.id
project_type_at_audit   ENUM('transport','distribution') NOT NULL
                        Snapshot of project.project_type at audit time
phase_at_audit          ENUM('APS','APD','PRO','EXE','DOE','FIN') NOT NULL
                        Snapshot of project.study_phase at audit time
                        Audit rules differ per phase
status                  ENUM('pending','running','completed','failed')
                        DEFAULT 'pending'
quality_score           DECIMAL(5,2) NULL  (0 to 100)
connectivity_score      DECIMAL(5,2) NULL  (weight: 40%)
coherence_score         DECIMAL(5,2) NULL  (weight: 30%)
capacity_score          DECIMAL(5,2) NULL  (weight: 20%)
extensibility_score     DECIMAL(5,2) NULL  (weight: 10%)
network_statistics      JSON NULL
                        Raw computed stats used for scoring
ai_summary              LONGTEXT NULL
                        AI-generated natural language interpretation
recommendations         LONGTEXT NULL
                        AI-generated list of improvements
anomaly_count           INT DEFAULT 0
critical_anomaly_count  INT DEFAULT 0
model_used              VARCHAR(100) NULL
tokens_used             INT NULL
error_message           TEXT NULL
started_at              TIMESTAMP NULL
completed_at            TIMESTAMP NULL
created_at              TIMESTAMP
updated_at              TIMESTAMP
```

**Audit history:**
- A project can have multiple audits over its lifetime
- Each audit is independent and immutable once completed
- `project_type_at_audit` and `phase_at_audit` preserve historical context
- Engineers can compare scores across phases and over time

**Score weights:**
- Connectivity: 40%
- Coherence: 30%
- Capacity: 20%
- Extensibility: 10%

**Score interpretation:**
| Score | Meaning |
|---|---|
| 90–100 | Excellent |
| 75–89 | Good |
| 50–74 | Acceptable |
| 0–49 | Non-compliant |

**Audit rules by project type:**
- Transport: flags missing SRO connections, backbone saturation, abnormally long routes
- Distribution: flags orphan PBO, BO without parent, capacity saturation, low-FO cables

**Audit rules by phase:**
- APS/APD: warnings only
- PRO/EXE: critical anomalies block advancement
- DOE/FIN: all anomalies must be resolved

---

#### ai_conversations
```
id                  BIGINT PK AUTO_INCREMENT
project_id          FK → projects.id
audit_id            FK → audits.id NULL
                    Optional — conversation may relate to a specific audit
user_id             FK → users.id
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

#### ai_messages
```
id                  BIGINT PK AUTO_INCREMENT
conversation_id     FK → ai_conversations.id
role                ENUM('user','assistant') NOT NULL
message             LONGTEXT NOT NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### 5.2 Eloquent Relationships

```
User
  hasMany → Projects (created_by)
  hasMany → Audits (performed_by)
  hasMany → AIConversations

Project
  belongsTo → User (created_by)
  belongsTo → Project (parent_project_id — transport parent)
  hasMany → Projects (distribution children)
  hasMany → ProjectDatasets
  hasMany → Audits
  hasMany → AIConversations

ProjectDataset
  belongsTo → Project
  hasMany → Audits

Audit
  belongsTo → Project
  belongsTo → ProjectDataset
  belongsTo → User (performed_by)
  hasMany → AIConversations

AIConversation
  belongsTo → User
  belongsTo → Project
  belongsTo → Audit (nullable)
  hasMany → AIMessages

AIMessage
  belongsTo → AIConversation
```

---

### 5.3 Enums

```php
// UserRole
admin | ingenieur

// ProjectType
transport | distribution

// ProjectStatus
draft | in_progress | audited | validated | archived

// StudyPhase
APS | APD | PRO | EXE | DOE | FIN

// AuditStatus
pending | running | completed | failed

// MessageRole
user | assistant
```

---

### 5.4 PostGIS — External GIS tables (read-only, not managed by FiberFlow)

| Table | Content |
|---|---|
| `t_noeud` | Topology nodes (all network points) |
| `t_ptech` | Technical points (PM, BO, PBO, NRO, SRO) |
| `t_cable` | Cables with fiber count |
| `t_cableline` | Cable geometry (LineString) |
| `t_cheminement` | Path geometry |
| `t_conduite` | Conduit/duct geometry |
| `t_ebp` | BPE/PBO equipment |
| `t_sitetech` | Technical sites |
| `t_znro` | NRO zone polygon |
| `t_zsro` | SRO zone polygon |
| `t_zpbo` | PBO zone polygon |

FiberFlow queries these with `DB::connection('postgis')`, filtered by `gis_project_id` (znro_code), via `ST_AsGeoJSON()` for geometry columns.

---

## 6. SERVICE LAYER

All business logic lives in Services. Controllers are lightweight — they receive requests and return responses only.

| Service | Responsibilities |
|---|---|
| `ProjectService` | Create, update, archive, retrieve projects |
| `GISService` | Read PostGIS, convert to GeoJSON, store ProjectDataset, search/filter network elements, serve GeoJSON to Leaflet |
| `AuditService` | Execute engineering rules, compute score breakdown, prepare AI context |
| `AIService` | Communicate with Groq, generate summary and recommendations, manage conversations |
| `ReportService` | Generate PDF (dompdf), generate Excel (maatwebsite) |
| `DashboardService` | Compute KPIs, aggregate stats, cache dashboard data |

---

## 7. JOBS (Queued)

| Job | Responsibility |
|---|---|
| `RunAuditJob` | Execute technical audit, analyze GeoJSON, compute quality score |
| `AnalyzeAuditJob` | Send audit results to Groq, store AI summary and recommendations |
| `GenerateReportJob` | Generate PDF/Excel, prepare download |

---

## 8. EVENTS & LISTENERS

One Event/Listener pair to demonstrate Laravel event-driven architecture:

| Event | Listener |
|---|---|
| `AuditCompleted` | `StoreAuditLogListener` — logs audit completion |

---

## 9. AI MODULE

### 9.1 Package
`laravel/ai` — official Laravel 13 AI SDK
Provider: Groq

### 9.2 AI Capabilities
- Interpret technical audit results in natural language
- Explain detected anomalies
- Generate improvement recommendations
- Answer contextual questions about the current project/audit

### 9.3 AI Input
The AI receives structured business data, not raw GIS:

```json
{
  "project": {
    "type": "distribution",
    "phase": "PRO"
  },
  "statistics": {
    "total_pbo": 140,
    "total_bo": 32,
    "total_cables": 58,
    "orphan_pbo": 3,
    "saturated_cables": 2
  },
  "anomalies": [
    "PBO-014 not connected to any BO",
    "Cable CB-032 at 94% fiber capacity"
  ]
}
```

### 9.4 AI Output (Structured)
```json
{
  "summary": "string — natural language audit interpretation",
  "quality": "string — overall quality assessment",
  "recommendations": ["string", "string"],
  "observations": ["string"] // optional
}
```

### 9.5 AI Chat
- Engineers can ask contextual questions per project/audit
- Conversations stored in `ai_conversations` + `ai_messages`
- Each conversation optionally linked to a specific audit
- The AI never modifies any data

### 9.6 AI Limitations
- Cannot modify GIS data
- Cannot create routes or edit infrastructure
- Does not communicate directly with PostGIS
- Only analyzes data prepared by `AuditService`

### 9.7 Error Handling
- If Groq is unavailable, audit remains valid
- Failure is logged
- User receives informative message
- AI analysis can be retried

---

## 10. API

### 10.1 Style
REST API, API First. Blade interface consumes same Services as the API.

### 10.2 Authentication
- Laravel Breeze → Blade session auth
- Laravel Sanctum → Bearer Token for REST API

### 10.3 Response Format
```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {}
}
```

### 10.4 Key Endpoints

**Auth**
```
POST   /api/login
POST   /api/logout
GET    /api/user
```

**Users (admin only)**
```
GET    /api/users
GET    /api/users/{id}
POST   /api/users
PUT    /api/users/{id}
DELETE /api/users/{id}
```

**Projects**
```
GET    /api/projects
GET    /api/projects/{id}
POST   /api/projects
PUT    /api/projects/{id}
DELETE /api/projects/{id}
```

**Dataset**
```
POST   /api/projects/{project}/dataset/import
GET    /api/projects/{project}/dataset
POST   /api/projects/{project}/dataset/reimport
```

**Audits**
```
POST   /api/projects/{project}/audits         → 202 Accepted (queued)
GET    /api/projects/{project}/audits
GET    /api/audits/{audit}
```

**AI**
```
POST   /api/ai/chat
GET    /api/ai/conversations/{conversation}
```

**Reports**
```
GET    /api/audits/{audit}/report/pdf
GET    /api/audits/{audit}/report/excel
```

**Dashboard**
```
GET    /api/dashboard
```

### 10.5 HTTP Status Codes
| Code | Meaning |
|---|---|
| 200 | Success |
| 201 | Created |
| 202 | Accepted (queued job) |
| 204 | No Content |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 409 | Conflict |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

---

## 11. CAPACITY DASHBOARD

Project-level screen computing occupation from `project_datasets.geojson`.

| Indicator | Description |
|---|---|
| Total equipments | Count per type (NRO/SRO/BO/PBO) |
| Total cables | Count with total fiber capacity |
| Fiber occupation rate | Used fibres / total fibres |
| Saturated cables | ≥ 80% occupation (warning) |
| Critical cables | ≥ 95% occupation (critical) |
| Orphan equipments | Equipments with no connections |

**Thresholds:**
| Level | Threshold | Color |
|---|---|---|
| Normal | < 80% | Green |
| Warning | 80–94% | Orange |
| Critical | ≥ 95% | Red |

Computed entirely from MySQL — no live PostGIS query during display.

---

---

## 12. SYNOPTIQUE

Visual representation of the logical network hierarchy, rendered from `project_datasets.geojson`.

**Technology:** Cytoscape.js (vanilla JS, no React required)

**Rendering by project type:**

| Type | Graph |
|---|---|
| Transport | NRO → SRO |
| Distribution | SRO → BO → PBO |

**Flow:**
1. PHP reads `project_datasets.geojson` and builds nodes + edges array
2. Array passed to Blade as a JS variable
3. Cytoscape.js renders the directed graph
4. Engineer can export via `cy.png()` → base64 PNG
5. PNG posted to Laravel controller → stored on disk
6. dompdf embeds PNG when generating PDF report

**Cytoscape node structure:**
```json
{
  "nodes": [
    {"data": {"id": "NRO-001", "label": "NRO-001", "type": "NRO"}},
    {"data": {"id": "SRO-001", "label": "SRO-001", "type": "SRO"}}
  ],
  "edges": [
    {"data": {"source": "NRO-001", "target": "SRO-001"}}
  ]
}
```

---

## 13. ADMIN MODULE

Accessible only to `role = admin`.

- **User Management** — CRUD users, assign roles
- **Project Management** — manage all projects across all users
- **Platform Statistics** — total projects, audits, average score, recent activity
- **Activity Log** — login/logout, project creation, audit launches, exports

---

## 13. PACKAGES

### Composer (production)
```
laravel/ai                    Laravel 13 AI SDK (Groq integration)
laravel/sanctum               API Bearer Token authentication
barryvdh/laravel-dompdf       PDF generation
maatwebsite/laravel-excel     Excel export
doctrine/dbal                 Schema operations
```

### Composer (development)
```
pestphp/pest                  Testing framework
laravel/telescope             Debugging
barryvdh/laravel-debugbar     Development toolbar
laravel/pail                  Log viewer
laravel/scribe                API documentation generation
```

### NPM
```
tailwindcss                   Utility CSS
alpinejs                      Client-side interactivity
leaflet                       Interactive map (read-only GeoJSON display)
chart.js                      Dashboard charts
cytoscape                     Synoptique graph rendering (NRO→SRO / SRO→BO→PBO)
```

### PHP extensions (server level)
```
ext-pdo_pgsql                 PHP PostgreSQL driver
ext-pdo_mysql                 PHP MySQL driver
```

---

## 14. POSTGIS SERVER SETUP (Docker)

### docker-compose.yml
```yaml
services:
  postgis:
    image: postgis/postgis:16-3.4
    container_name: fiberflow_postgis
    environment:
      POSTGRES_DB: gracethd_db
      POSTGRES_USER: gracethd_user
      POSTGRES_PASSWORD: gracethd_pass
    ports:
      - "5432:5432"
    volumes:
      - postgis_data:/var/lib/postgresql/data
    restart: unless-stopped

volumes:
  postgis_data:
```

### Start container
```bash
docker-compose up -d
```

### Verify PostGIS
```bash
docker exec -it fiberflow_postgis psql -U gracethd_user -d gracethd_db -c "SELECT PostGIS_Version();"
```

### QGIS connection settings
| Field | Value |
|---|---|
| Host | localhost |
| Port | 5432 |
| Database | gracethd_db |
| Username | gracethd_user |
| Password | gracethd_pass |
| SSL mode | disable |

### .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fiberflow
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

POSTGIS_HOST=127.0.0.1
POSTGIS_PORT=5432
POSTGIS_DATABASE=gracethd_db
POSTGIS_USERNAME=gracethd_user
POSTGIS_PASSWORD=gracethd_pass
```

---

## 15. GraceTHD TABLES IN POSTGIS

Minimum required tables for demo:

```sql
CREATE TABLE t_noeud (
    nd_code     VARCHAR(254) PRIMARY KEY,
    nd_nom      VARCHAR(254),
    nd_type     VARCHAR(50),
    geom        GEOMETRY(Point, 2154)
);

CREATE TABLE t_ptech (
    pt_code     VARCHAR(254) PRIMARY KEY,
    pt_nd_code  VARCHAR(254) REFERENCES t_noeud(nd_code),
    pt_typephy  VARCHAR(50),
    pt_typelog  VARCHAR(50),
    pt_etat     VARCHAR(50),
    pt_avct     VARCHAR(50),
    pt_nature   VARCHAR(50),
    pt_prop     VARCHAR(254),
    pt_gest     VARCHAR(254)
);

CREATE TABLE t_cable (
    cb_code     VARCHAR(254) PRIMARY KEY,
    cb_fo       INTEGER,
    cb_typelog  VARCHAR(50),
    cb_etat     VARCHAR(50),
    cb_prop     VARCHAR(254)
);

CREATE TABLE t_cableline (
    cl_code     VARCHAR(254) PRIMARY KEY,
    cl_cb_code  VARCHAR(254) REFERENCES t_cable(cb_code),
    geom        GEOMETRY(LineString, 2154)
);

CREATE TABLE t_cheminement (
    ch_code     VARCHAR(254) PRIMARY KEY,
    ch_typ      VARCHAR(50),
    geom        GEOMETRY(LineString, 2154)
);

CREATE TABLE t_conduite (
    cd_code     VARCHAR(254) PRIMARY KEY,
    cd_typ      VARCHAR(50),
    cd_dia_int  DECIMAL(6,2),
    geom        GEOMETRY(LineString, 2154)
);

CREATE TABLE t_ebp (
    bp_code     VARCHAR(254) PRIMARY KEY,
    bp_nd_code  VARCHAR(254) REFERENCES t_noeud(nd_code),
    bp_typephy  VARCHAR(50),
    bp_typelog  VARCHAR(50),
    bp_etat     VARCHAR(50),
    bp_prop     VARCHAR(254)
);

CREATE TABLE t_sitetech (
    st_code     VARCHAR(254) PRIMARY KEY,
    st_nd_code  VARCHAR(254) REFERENCES t_noeud(nd_code),
    st_typ      VARCHAR(50),
    st_etat     VARCHAR(50),
    st_prop     VARCHAR(254)
);

CREATE TABLE t_znro (
    zn_code     VARCHAR(254) PRIMARY KEY,
    zn_nd_code  VARCHAR(254),
    geom        GEOMETRY(MultiPolygon, 2154)
);

CREATE TABLE t_zsro (
    zs_code     VARCHAR(254) PRIMARY KEY,
    zs_nd_code  VARCHAR(254),
    geom        GEOMETRY(MultiPolygon, 2154)
);

CREATE TABLE t_zpbo (
    zp_code     VARCHAR(254) PRIMARY KEY,
    zp_nd_code  VARCHAR(254),
    geom        GEOMETRY(MultiPolygon, 2154)
);

-- Spatial indexes
CREATE INDEX idx_noeud_geom       ON t_noeud       USING GIST(geom);
CREATE INDEX idx_cableline_geom   ON t_cableline   USING GIST(geom);
CREATE INDEX idx_cheminement_geom ON t_cheminement USING GIST(geom);
CREATE INDEX idx_conduite_geom    ON t_conduite    USING GIST(geom);
CREATE INDEX idx_znro_geom        ON t_znro        USING GIST(geom);
CREATE INDEX idx_zsro_geom        ON t_zsro        USING GIST(geom);
CREATE INDEX idx_zpbo_geom        ON t_zpbo        USING GIST(geom);
```

SRID 2154 = RGF93 / Lambert-93 (French national projection used by GraceTHD).

### Import ANFI-BAUGE01 demo data (ogr2ogr)
```bash
ogr2ogr -f "PostgreSQL" \
  PG:"host=127.0.0.1 dbname=gracethd_db user=gracethd_user password=gracethd_pass" \
  /path/to/PIVOT/noeud.shp -nln t_noeud -overwrite

ogr2ogr -f "PostgreSQL" \
  PG:"host=127.0.0.1 dbname=gracethd_db user=gracethd_user password=gracethd_pass" \
  /path/to/PIVOT/cable.shp -nln t_cableline -overwrite
```

---

## 16. GISService IMPORT QUERY EXAMPLE

```php
$data = [
    't_noeud' => DB::connection('postgis')->select(
        'SELECT nd_code, nd_nom, nd_type, ST_AsGeoJSON(geom) as geom
         FROM t_noeud
         WHERE nd_code IN (
             SELECT nd_code FROM t_znro WHERE zn_code = ?
         )', [$project->gis_project_id]
    ),
    't_ptech' => DB::connection('postgis')->select(
        'SELECT pt_code, pt_nd_code, pt_typephy, pt_typelog, pt_etat, pt_avct, pt_nature
         FROM t_ptech
         WHERE pt_nd_code IN (
             SELECT nd_code FROM t_znro WHERE zn_code = ?
         )', [$project->gis_project_id]
    ),
    // ... same pattern for all tables
];
```

---

## 17. BUSINESS RULES SUMMARY

| Rule | Description |
|---|---|
| BR-01 | Only authenticated users can access the application |
| BR-02 | Only admins can create/update/delete users |
| BR-03 | Only admins can create/archive projects |
| BR-04 | Engineers can view projects, launch audits, use AI, download reports |
| BR-05 | `project_type` is immutable after creation |
| BR-06 | Transport projects: `parent_project_id` must be NULL |
| BR-07 | Distribution projects: `parent_project_id` must point to a transport project |
| BR-08 | No PBO allowed in transport projects |
| BR-09 | FiberFlow never creates or modifies GIS data |
| BR-10 | All GIS modifications happen in QGIS only |
| BR-11 | An audit requires an imported dataset to exist |
| BR-12 | Audit is executed asynchronously via Laravel Queues |
| BR-13 | Audit stores `project_type_at_audit` and `phase_at_audit` at time of execution |
| BR-14 | Re-import soft-deletes old dataset — audit history preserved via `projectdataset_id` |
| BR-15 | A project can have multiple audits — full history preserved |
| BR-16 | AI never modifies project data |
| BR-17 | Reports are generated on demand — not permanently stored |
| BR-18 | Dashboard data may be cached |
| BR-19 | All API inputs validated via Form Requests |
| BR-20 | Authorization enforced via Laravel Policies |
| BR-21 | Capacity warning threshold: ≥ 80% fiber occupation |
| BR-22 | Capacity critical threshold: ≥ 95% fiber occupation |
| BR-23 | Score weights: connectivity 40%, coherence 30%, capacity 20%, extensibility 10% |
| BR-24 | Projects are archived (soft delete), never hard deleted |

---

## 18. DEVELOPMENT ROADMAP

| Phase | Tasks |
|---|---|
| 1 | Laravel setup, Docker, Git, Breeze, Sanctum, MySQL config |
| 2 | Auth — login, logout, Sanctum tokens, Policies, Middleware |
| 3 | User CRUD — admin only, Form Requests, Resources, Pest tests |
| 4 | Project CRUD — parent project logic, Policies, Resources, tests |
| 5 | GISService — PostGIS connection, import, GeoJSON storage |
| 6 | Leaflet — read-only map from project_datasets GeoJSON |
| 7 | AuditService — engineering rules, score, queued jobs |
| 8 | AIService — Groq integration, structured output, chat |
| 9 | Dashboard — KPIs, Chart.js, caching |
| 10 | Reports — dompdf PDF, maatwebsite Excel |
| 11 | Pest — feature + unit tests, Queue::fake(), AI mocks |
| 12 | Docker — Dockerfile, docker-compose full stack |
| 13 | GitHub Actions — CI pipeline |
| 14 | Azure — deployment, Nginx, Supervisor |
| 15 | Scribe — API documentation |
| 16 | README, MCD, MLD, presentation slides |

---

## 19. FUTURE SCOPE (V2)

| Feature | Status |
|---|---|
| Network snapshots with delta comparison | Deferred |
| Rule Engine (configurable audit rules) | Deferred |
| Synoptique graph (Cytoscape.js) | Deferred |
| Route tracing module | Deferred |
| bureau_etudes multi-tenant layer | Deferred |
| Advanced AI (multi-turn reasoning, RAG) | Deferred |
| Redis + Laravel Horizon | Deferred |
| Vue.js / React frontend | Deferred |
| Mobile application | Deferred |
| Continuous Deployment (CD) | Deferred |
| Real-time notifications | Deferred |
