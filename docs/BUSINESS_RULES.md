# Business Rules

## FiberFlow

Version 1.0

---

# 1. Purpose

This document defines the business rules governing the FiberFlow application.

Business rules describe the functional constraints that every module of the application must respect regardless of the technical implementation.

---

# 2. Authentication

## BR-001

Only authenticated users can access protected resources.

---

## BR-002

Authentication is performed using:

- Laravel Sanctum for the REST API and for the web interface

---

# 3. User Management

## BR-003

Only administrators can manage users.

---

## BR-004

An engineer cannot create, modify or delete users.

---

## BR-005

Each user must have exactly one role.

Available roles:

- Administrator
- Engineer

---

# 4. Projects

## BR-006

A project must have:

- a name
- a project type
- a study phase

before it can be created.

---

## BR-007

A project type is immutable after creation.

Possible values:

- Transport
- Distribution

---

## BR-008

A Distribution project must belong to exactly one Transport project.

---

## BR-009

A Transport project cannot belong to another project.

---

## BR-010

A project follows the FTTH study lifecycle:

APS

↓

APD

↓

PRO

↓

EXE

↓

DOE

↓

FIN

---

## BR-011

Projects can only be archived.

Archived projects cannot be modified.

---

# 5. GIS Dataset

## BR-012

FiberFlow never edits GIS data.

All geographic modifications must be performed in QGIS.

---

## BR-013

Each project owns one imported dataset.

---

## BR-014

The imported dataset is stored as GeoJSON.

---

## BR-015

A project must contain an imported dataset before an audit can be executed.

---

## BR-016

A dataset can be re-imported at any time.

The previous imported dataset is replaced.

---

# 6. Technical Audit

## BR-017

An audit always analyzes the imported dataset associated with the project.

---

## BR-018

An audit produces:

- quality score
- detected anomalies
- AI summary
- AI recommendations

---

## BR-019

Audit results are permanently stored.

---

## BR-020

Launching an audit is an asynchronous operation executed through Laravel Queues.

---

# 7. Artificial Intelligence

## BR-021

The AI never modifies project data.

---

## BR-022

The AI only analyzes the imported GeoJSON dataset.

---

## BR-023

The AI provides:

- anomaly explanations
- recommendations
- contextual answers

---

## BR-024

AI responses are stored with the audit.

---

# 8. Reports

## BR-025

Reports are generated on demand.

---

## BR-026

Generated reports are not permanently stored.

---

# 9. Dashboard

## BR-027

Dashboard statistics are calculated from stored audit results.

---

## BR-028

Frequently accessed dashboard data may be cached.

---

# 10. Security

## BR-029

Authorization is enforced through Laravel Policies.

---

## BR-030

All API inputs must be validated using Form Requests.

---

## BR-031

Protected endpoints require a valid Sanctum token.

---

## BR-032

API endpoints are protected using Rate Limiting.

---

# 11. Data Integrity

## BR-033

Business operations affecting multiple tables must use database transactions.

---

## BR-034

Deleting a project must never delete historical audits.

---

## BR-035

Every audit belongs to exactly one project.

---

## BR-036

Every AI conversation belongs to exactly one project.

---

## BR-037

An AI conversation may optionally reference one audit.

---

# 12. Future Evolution

The following features are intentionally excluded from Version 1:

- GIS editing
- Network snapshots
- Rule Engine
- Advanced AI predictions
- Mobile application
- Continuous Deployment
- Laravel Horizon
