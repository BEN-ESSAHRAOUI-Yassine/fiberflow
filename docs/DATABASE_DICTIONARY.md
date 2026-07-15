# Database Dictionary

## FiberFlow

Version 1.0

---

# 1. Purpose

This document describes every table, column, relationship and business meaning used in the FiberFlow database.

The database stores only application data.

GIS data remains managed by PostgreSQL/PostGIS and QGIS.

---

# 2. users

Stores application users.

## Columns

| Column     | Type      | Description               |
| ---------- | --------- | ------------------------- |
| id         | bigint    | Primary Key               |
| name       | string    | User full name            |
| email      | string    | User email address        |
| password   | string    | Hashed password           |
| role       | enum      | Administrator or Engineer |
| created_at | timestamp | Creation date             |
| updated_at | timestamp | Last update               |

---

## Relationships

- hasMany Projects
- hasMany Audits
- hasMany AIConversations

---

# 3. projects

Stores FTTH engineering projects.

## Columns

| Column            | Type            | Description                    |
| ----------------- | --------------- | ------------------------------ |
| id                | bigint          | Primary Key                    |
| name              | string          | Project name                   |
| description       | text            | Project description            |
| client            | string          | Customer                       |
| municipality      | string          | Project location               |
| project_type      | enum            | Transport or Distribution      |
| study_phase       | enum            | APS, APD, PRO, EXE, DOE or FIN |
| parent_project_id | bigint nullable | Parent Transport project       |
| status            | enum            | Project status                 |
| created_by        | bigint          | Project creator                |
| created_at        | timestamp       | Creation date                  |
| updated_at        | timestamp       | Last update                    |

---

## Relationships

- belongsTo User
- belongsTo Project
- hasMany Projects
- hasOne ProjectDataset
- hasMany Audits
- hasMany AIConversations

---

# 4. project_datasets

Stores the imported GeoJSON dataset used by FiberFlow.

This table represents the application's local copy of the GIS network.

The original GIS data remains inside PostgreSQL/PostGIS.

---

## Columns

| Column      | Type      | Description              |
| ----------- | --------- | ------------------------ |
| id          | bigint    | Primary Key              |
| project_id  | bigint    | Associated project       |
| geojson     | jsonb     | Imported GeoJSON dataset |
| imported_at | timestamp | Import date              |
| created_at  | timestamp | Creation date            |
| updated_at  | timestamp | Last update              |

---

## Relationships

- belongsTo Project

---

## Notes

- Only one dataset exists per project.
- Re-import replaces the previous dataset.
- GeoJSON is read-only inside FiberFlow.
- Geographic editing is performed exclusively in QGIS.

---

# 5. audits

Stores technical audit results.

---

## Columns

| Column             | Type      | Description        |
| ------------------ | --------- | ------------------ |
| id                 | bigint    | Primary Key        |
| project_id         | bigint    | Audited project    |
| performed_by       | bigint    | Engineer           |
| quality_score      | integer   | Final score        |
| status             | enum      | Audit status       |
| ai_summary         | longText  | AI interpretation  |
| recommendations    | longText  | AI recommendations |
| network_statistics | json      | Audit statistics   |
| started_at         | timestamp | Audit start        |
| completed_at       | timestamp | Audit completion   |
| created_at         | timestamp | Creation date      |
| updated_at         | timestamp | Last update        |

---

## Relationships

- belongsTo Project
- belongsTo User

---

## Notes

The audit stores only its results.

The imported GeoJSON remains inside the Project Dataset.

---

# 6. ai_conversations

Stores AI conversations.

---

## Columns

| Column     | Type            | Description        |
| ---------- | --------------- | ------------------ |
| id         | bigint          | Primary Key        |
| project_id | bigint          | Related project    |
| audit_id   | bigint nullable | Related audit      |
| user_id    | bigint          | Conversation owner |
| created_at | timestamp       | Creation date      |
| updated_at | timestamp       | Last update        |

---

## Relationships

- belongsTo User
- belongsTo Project
- belongsTo Audit
- hasMany AIMessages

---

# 7. ai_messages

Stores conversation messages.

---

## Columns

| Column          | Type      | Description         |
| --------------- | --------- | ------------------- |
| id              | bigint    | Primary Key         |
| conversation_id | bigint    | Parent conversation |
| role            | enum      | User or Assistant   |
| message         | longText  | Message content     |
| created_at      | timestamp | Creation date       |
| updated_at      | timestamp | Last update         |

---

## Relationships

- belongsTo AIConversation

---

# 8. Enums

## UserRole

| Value         |
| ------------- |
| Administrator |
| Engineer      |

---

## ProjectType

| Value        |
| ------------ |
| Transport    |
| Distribution |

---

## ProjectStatus

| Value       |
| ----------- |
| Draft       |
| In Progress |
| Audited     |
| Validated   |
| Archived    |

---

## StudyPhase

| Value |
| ----- |
| APS   |
| APD   |
| PRO   |
| EXE   |
| DOE   |
| FIN   |

---

## AuditStatus

| Value     |
| --------- |
| Pending   |
| Running   |
| Completed |
| Failed    |

---

## MessageRole

| Value     |
| --------- |
| User      |
| Assistant |

---

# 9. External GIS Database

FiberFlow does **not** manage the FTTH network database.

The following spatial data is stored in PostgreSQL/PostGIS and maintained using QGIS:

- NRO
- SRO
- BO
- PBO
- Optical Routes
- Infrastructure Routes
- Chambers
- Poles
- Conduits

FiberFlow imports the project network as GeoJSON into the `project_datasets` table for analysis and AI processing.

---

# 10. Referential Integrity

## User

A user cannot be deleted if referenced by projects, audits or AI conversations.

---

## Project

Deleting a project must never remove historical audits.

Projects should be archived instead of deleted.

---

## Project Dataset

Each project owns exactly one dataset.

A dataset cannot exist without a project.

---

## Audit

Every audit belongs to one project.

Every audit is performed by one engineer.

---

## AI Conversation

Every conversation belongs to one project.

An audit reference is optional.

---

## AI Message

Every message belongs to exactly one conversation.
