# Architecture Decisions

## ADR-001

QGIS remains the only GIS editing software.

Reason:
FiberFlow focuses on audit and AI rather than GIS editing.

---

## ADR-002

GeoJSON is stored inside project_datasets.

Reason:
Decouple the audit engine from PostGIS.

---

## ADR-003

Snapshots are postponed.

Reason:
Reduce project complexity while keeping the architecture extensible.

---

## ADR-004

API First architecture.

Reason:
Respect backend best practices and simplify future frontend evolution.

---

## ADR-005

Laravel Breeze + Sanctum.

Reason:
Blade for demonstration.
Sanctum for API authentication.

---

## ADR-006

Audit uses queued jobs.

Reason:
AI analysis is asynchronous.

---

## ADR-007

Dataset import is synchronous.

Reason:
Project datasets are small enough for Version 1.
