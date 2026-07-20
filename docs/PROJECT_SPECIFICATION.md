# FiberFlow - Project Specification

**Version:** 1.0.0  
**Author:** Yassine BEN ESSAHRAOUI  
**Project Type:** Backend Fil Rouge  
**Framework:** Laravel 13  
**Language:** PHP 8.3  
**Database:** mysql + PostgreSQL + PostGIS  
**Architecture:** API First  
**AI Provider:** Groq (Laravel AI SDK)

# FiberFlow - Project Summary

| Item                    | Description                                                                                                    |
| ----------------------- | -------------------------------------------------------------------------------------------------------------- |
| **Project Name**        | FiberFlow                                                                                                      |
| **Problem**             | FTTH engineering studies are manually audited, making the validation process slow, repetitive and error-prone. |
| **Target Users**        | FTTH Engineers and Project Administrators                                                                      |
| **Backend Type**        | REST API (API First)                                                                                           |
| **Frontend**            | Blade (Demonstration Interface)                                                                                |
| **AI Role**             | Interpret technical audits, explain anomalies, answer contextual questions and provide recommendations         |
| **AI Provider**         | Groq using Laravel AI SDK                                                                                      |
| **GIS Source**          | PostgreSQL + PostGIS managed through QGIS                                                                      |
| **Main Goal**           | Assist engineers in validating FTTH studies without modifying GIS data                                         |
| **Project Inspiration** | Real FTTH engineering workflows used by telecommunications design offices                                      |

---

# Table of Contents

1. Project Overview
2. Problem Statement
3. Objectives
4. Target Users
5. Project Scope
6. FTTH Domain
7. Functional Requirements
8. Non-Functional Requirements
9. User Roles
10. Database Structure
11. Eloquent Relationships
12. Business Rules
13. Architecture Overview
14. Laravel Architecture
15. Laravel Concepts Used
16. Packages
17. Services
18. Jobs
19. Events & Listeners
20. API Strategy
21. Development Roadmap
22. Future Improvements

---

# 1. Project Overview

## Project Name

**FiberFlow**

## Description

FiberFlow is an API-first platform designed to supervise, audit and analyze FTTH (Fiber To The Home) engineering studies.

The application reads network data produced by QGIS and stored in PostgreSQL/PostGIS in order to perform automated technical audits.

FiberFlow does **not** edit GIS data.

All modifications of the optical network remain the responsibility of QGIS.

FiberFlow focuses on:

- project management
- technical audits
- AI-assisted interpretation
- report generation
- project supervision

A lightweight Blade interface is provided only for demonstration purposes.

The REST API remains the primary product of the application.

---

# 2. Problem Statement

FTTH engineering offices usually work with QGIS and PostgreSQL/PostGIS to design optical networks.

Although these tools are excellent for producing geographic data, they do not provide automated verification of engineering rules nor intelligent interpretation of detected anomalies.

As a result:

- audits are mostly manual
- engineers spend significant time reviewing projects
- anomalies may remain undetected
- project quality depends heavily on individual experience

FiberFlow addresses this problem by providing an API capable of auditing FTTH studies and generating AI-assisted recommendations.

---

# 3. Objectives

## Main Objective

Provide an API capable of auditing FTTH engineering studies using GIS data stored in PostgreSQL/PostGIS.

---

## Secondary Objectives

- Centralize FTTH projects
- Manage study lifecycle
- Launch technical audits
- Interpret audit results with AI
- Generate downloadable reports
- Provide a demonstration interface
- Expose a clean REST API

---

# 4. Target Users

## Administrator

Responsible for:

- managing users
- managing projects
- launching audits
- consulting reports

---

## Engineer

Responsible for:

- consulting projects
- launching audits
- consulting AI recommendations
- downloading reports
- interacting with the AI Assistant

---

# 5. Project Scope

## Included

- REST API
- Blade demonstration interface
- Authentication with Sanctum
- User management
- Project management
- Read-only consultation of GIS data
- Technical audits
- AI-assisted interpretation
- AI contextual discussion
- PDF reports
- Excel export
- Dashboard
- Docker
- GitHub Actions
- Azure deployment

## Excluded

- GIS editing
- Route creation
- Infrastructure editing
- Optical equipment editing
- Replacement of QGIS

FiberFlow only consumes GIS data stored in PostgreSQL/PostGIS. All editing operations remain the responsibility of QGIS.

---

# 6. FTTH Domain

## 6.1 Network Hierarchy

### Transport Network

```text
NRO
└── SRO
```

### Distribution Network

```text
SRO
└── BO
     └── PBO
```

---

## 6.2 Project Types

| Type         | Description    |
| ------------ | -------------- |
| Transport    | NRO → SRO      |
| Distribution | SRO → BO → PBO |

### Business Rule

A Distribution project must belong to a parent Transport project.

A project cannot be both Transport and Distribution.

---

## 6.3 Study Phases

| Phase | Description                   |
| ----- | ----------------------------- |
| APS   | Avant-Projet Sommaire         |
| APD   | Avant-Projet Détaillé         |
| PRO   | Projet                        |
| EXE   | Exécution                     |
| DOE   | Dossier des Ouvrages Exécutés |
| FIN   | Fin / Réception               |

The study phase influences audit rules.

Some anomalies tolerated during APS become critical during EXE.

---

## 6.4 Route Types

### Optical Route

Contains:

- NRO
- SRO
- BO
- PBO

---

### Infrastructure Route

Contains:

- Chambers
- Poles
- Conduits
- Civil engineering infrastructure

---

# 7. Functional Requirements

## Authentication

- Login
- Logout
- Bearer Token Authentication
- Route protection

---

## User Management

Administrator can:

- Create users
- Update users
- Delete users
- View users

---

## Project Management

Administrator can:

- Create project
- Update project
- Archive project
- View projects

Engineer can:

- View projects
- Launch audits
- Download reports

---

## Network Consultation

The application allows users to:

- View network information
- Search network elements
- Filter network data
- Display GIS information

The network remains read-only.

---

## Technical Audit

Users can:

- Launch an audit
- View detected anomalies
- View quality score
- Generate reports

---

## AI Assistant

The AI Assistant can:

- Interpret audit results
- Explain anomalies
- Suggest improvements
- Answer contextual questions about the current audit

---

## Dashboard

Dashboard displays:

- Total projects
- Last audit
- Quality score
- Recent audits

# 8. Non-Functional Requirements

## Performance

- API responses should remain fast under normal usage.
- Long-running operations must be executed asynchronously using Laravel Jobs and Queues.
- Dashboard statistics should use caching when appropriate.

---

## Security

- Authentication using Laravel Sanctum.
- Passwords encrypted using Laravel Hash.
- Protected API routes.
- Authorization using Policies.
- Rate limiting on API endpoints.
- Input validation using Form Requests.

---

## Maintainability

The application must follow Laravel best practices:

- MVC Architecture
- Service Layer
- Form Requests
- API Resources
- Enums
- Eloquent ORM
- Reusable business logic

---

## Scalability

The architecture should allow future integration of:

- Redis
- Laravel Horizon
- Rule Engine
- Continuous Deployment
- Mobile application

---

## Reliability

The application must:

- Handle exceptions properly.
- Return appropriate HTTP status codes.
- Preserve database consistency using transactions.

---

## Testability

The project must include Pest tests covering:

- Authentication
- CRUD operations
- Validation
- Protected routes
- Jobs
- AI integration (faked)

---

# 9. User Stories

## Authentication

### US-01

**As an Administrator**

I want to log in

So that I can access the administration interface.

---

### US-02

**As an Engineer**

I want to authenticate using a Bearer Token

So that I can securely consume the API.

---

# User Management

### US-03

**As an Administrator**

I want to create users

So that engineers can access the application.

---

### US-04

**As an Administrator**

I want to edit user information

So that user accounts remain up to date.

---

### US-05

**As an Administrator**

I want to deactivate users

So that unauthorized users cannot access the application.

---

# Project Management

### US-06

**As an Administrator**

I want to create a Transport project

So that I can supervise an FTTH backbone study.

---

### US-07

**As an Administrator**

I want to create a Distribution project

So that I can supervise the distribution network of an existing Transport project.

---

### US-08

**As an Administrator**

I want to modify project information

So that project metadata remains accurate.

---

### US-09

**As an Administrator**

I want to archive a project

So that completed projects are no longer editable.

---

### US-10

**As an Engineer**

I want to consult project information

So that I can analyze the study.

---

# Network Consultation

### US-11

**As an Engineer**

I want to visualize network information

So that I can understand the project structure.

---

### US-12

**As an Engineer**

I want to search network elements

So that I can quickly locate equipment.

---

### US-13

**As an Engineer**

I want to filter displayed data

So that I only view relevant information.

---

# Technical Audit

### US-14

**As an Engineer**

I want to launch a technical audit

So that engineering anomalies are automatically detected.

---

### US-15

**As an Engineer**

I want to view detected anomalies

So that I know what must be corrected.

---

### US-16

**As an Engineer**

I want to obtain a quality score

So that I can quickly evaluate the project.

---

### US-17

**As an Engineer**

I want to generate an audit report

So that I can share the audit results.

---

# AI Assistant

### US-18

**As an Engineer**

I want AI to explain audit results

So that I better understand detected anomalies.

---

### US-19

**As an Engineer**

I want AI to suggest possible improvements

So that I can improve the network quality.

---

### US-20

**As an Engineer**

I want to ask contextual questions about the current audit

So that I can better understand specific situations before making engineering decisions.

---

# Dashboard

### US-21

**As an Engineer**

I want to view project statistics

So that I can quickly assess project health.

---

# 10. Database Structure

## Database responsibilities

### PostgreSQL + PostGIS (GIS Source)

This database is not managed by FiberFlow.

Its only responsibility is storing the engineering network.

- NRO
- SRO
- BO
- PBO
- Optical Routes
- Infrastructure Routes
- Chambers
- Poles
- Conduits

FiberFlow only reads from it.

No INSERT.

No UPDATE.

No DELETE.

All editing happens inside QGIS.

### MySQL (FiberFlow Application)

This is the application's database.

- users
- projects
- project_datasets
- audits
- ai_conversations
- ai_messages

Everything Laravel creates belongs here.

Workflow
Engineer

↓

Create Project

↓

MySQL
projects

Later:

Engineer

↓

Import Dataset

↓

Read PostGIS

↓

Convert to GeoJSON

↓

Store

↓

MySQL
project_datasets

Then:

Engineer

↓

Launch Audit

↓

Read GeoJSON

(from project_datasets)

↓

AuditService

↓

AI

↓

Store Results

↓

MySQL
audits

Notice something important:

After the import,

the audit no longer needs PostgreSQL.

Everything it needs is already inside MySQL.

### Why this is good

Suppose tomorrow:

PostGIS server crashes

You can still:

View projects ✅
View audits ✅
Open previous reports ✅
Chat with AI about previous audits ✅

The only unavailable feature would be:

Import Dataset

which is perfectly acceptable because that's the only operation that depends on PostGIS.

Think of project_datasets as a cache

It's not the source of truth.

It's an imported working copy.

PostGIS
(Source of Truth)

        │

Import

        ▼

project_datasets
(Working Copy)

        │

Audit

        │

AI

        │

Reports

This pattern is very common in enterprise applications.

## Database

**this application database**

- mysql

**Engine**

- PostgreSQL

**Spatial Extension**

- PostGIS

---

## Application Tables

### users

| Column     | Type      |
| ---------- | --------- |
| id         | bigint    |
| name       | string    |
| email      | string    |
| password   | string    |
| role       | enum      |
| created_at | timestamp |
| updated_at | timestamp |

---

### projects

| Column            | Type            |
| ----------------- | --------------- |
| id                | bigint          |
| name              | string          |
| description       | text            |
| client            | string          |
| municipality      | string          |
| project_type      | enum            |
| study_phase       | enum            |
| parent_project_id | bigint nullable |
| gis_project_id    | string          |
| status            | enum            |
| created_by        | bigint          |
| created_at        | timestamp       |
| updated_at        | timestamp       |

---

### projectdatasets

| Column      | Type      |
| ----------- | --------- |
| id          | bigint    |
| project_id  | FK        |
| geojson     | JSONB     |
| imported_at | timestamp |
| created_at  | timestamp |
| updated_at  | timestamp |

---

### audits

| Column             | Type      |
| ------------------ | --------- |
| id                 | bigint    |
| projectdataset_id  | bigint    |
| performed_by       | bigint    |
| quality_score      | integer   |
| status             | enum      |
| ai_summary         | longText  |
| recommendations    | longText  |
| network_statistics | json      |
| started_at         | timestamp |
| completed_at       | timestamp |
| created_at         | timestamp |
| updated_at         | timestamp |

---

### ai_conversations

| Column     | Type            |
| ---------- | --------------- |
| id         | bigint          |
| project_id | bigint          |
| audit_id   | bigint nullable |
| user_id    | bigint          |
| created_at | timestamp       |
| updated_at | timestamp       |

---

### ai_messages

| Column          | Type      |
| --------------- | --------- |
| id              | bigint    |
| conversation_id | bigint    |
| role            | enum      |
| message         | longText  |
| created_at      | timestamp |
| updated_at      | timestamp |

---

## External GIS Data

The following spatial tables are **not managed by FiberFlow**.

They are maintained directly inside PostgreSQL/PostGIS through QGIS.

Examples include:

- routes
- cables
- nro
- sro
- bo
- pbo
- chambers
- poles
- conduits

FiberFlow only reads these datasets during project consultation and technical audits.

---

# 11. Eloquent Relationships

## User

- hasMany Projects
- hasMany Audits
- hasMany AIConversations

---

## Project

- belongsTo User (creator)
- belongsTo Project (parent transport project)
- hasMany Projects (distribution projects)
- hasMany Projectdatasets

---

## ProjectDataset

- belongsTo Project
- hasMany Audits

---

## Audit

- belongsTo Project
- belongsTo User
- hasMany AIConversations

---

## AIConversation

- belongsTo User
- belongsTo Project
- belongsTo Audit
- hasMany AIMessages

---

## AIMessage

- belongsTo AIConversation

---

# 12. Enums

## UserRole

- Admin
- Engineer

---

## ProjectType

- Transport
- Distribution

---

## ProjectStatus

- Draft
- In Progress
- Audited
- Validated
- Archived

---

## StudyPhase

- APS
- APD
- PRO
- EXE
- DOE
- FIN

---

## AuditStatus

- Pending
- Running
- Completed
- Failed

---

## MessageRole

- user
- assistant

# 13. Business Rules

## BR-01 — Authentication

Only authenticated users can access the application.

---

## BR-02 — Authorization

Only Administrators can:

- Create users
- Update users
- Delete users
- Create projects
- Archive projects

Engineers have read-only access to project management features and can launch audits.

---

## BR-03 — Project Type

A project must be either:

- Transport
- Distribution

A project cannot belong to both categories.

---

## BR-04 — Parent Project

A Distribution project must belong to exactly one Transport project.

A Transport project cannot have a parent project.

---

## BR-05 — Study Phase

Every project must have one study phase:

- APS
- APD
- PRO
- EXE
- DOE
- FIN

The study phase influences the audit rules and anomaly severity.

---

## BR-06 — GIS Data

FiberFlow never creates or modifies GIS data.

GIS data is managed exclusively by QGIS and stored in PostgreSQL/PostGIS.

---

## BR-07 — Read-Only Network

Network elements are available in read-only mode.

Every modification of the optical network must be performed in QGIS.

---

## BR-08 — Audit

An audit is always executed using the current GIS data stored in PostgreSQL/PostGIS.

FiberFlow stores only the audit result.

It does not duplicate the GIS dataset.

---

## BR-09 — AI Assistant

The AI Assistant never modifies project data.

Its role is limited to:

- interpreting audit results
- explaining detected anomalies
- suggesting improvements
- answering contextual questions

---

## BR-10 — Reports

Reports are generated on demand.

They are not permanently stored in the database.

---

## BR-11 — Dashboard

Dashboard indicators are generated from audit results and project information.

---

# 14. Architecture Overview

## Architecture Style

FiberFlow follows an **API First** architecture.

The REST API represents the core of the application.

A Blade interface is provided exclusively for demonstration purposes.

Both interfaces consume the same business logic through shared Services.

---

## Global Architecture

```text
                    Browser

                       │

               Blade Interface

                       │

               Web Controllers

                       │

────────────────────────────────────────────

                  Services

────────────────────────────────────────────

                       ▲

               API Controllers

                       ▲

                  REST API

                       ▲

                  Laravel Sanctum

                       ▲

            PostgreSQL + PostGIS

                       ▲

                      QGIS
```

---

## Background Processing

```text
Controller

     │

Dispatch Job

     │

Queue

     │

Supervisor

     │

Job Execution
```

---

## AI Flow

```text
Engineer

     │

Launch Audit

     │

AuditService

     │

Read GIS Data

     │

Business Validation

     │

Groq AI

     │

AI Interpretation

     │

Audit Result
```

---

# 15. Laravel Architecture

## Architectural Pattern

FiberFlow follows the Laravel MVC architecture while separating business logic into dedicated Services.

```text
Client

     │

Controllers

     │

Form Requests

     │

Services

     │

Eloquent Models

     │

PostgreSQL
```

---

## Controllers

Two controller layers are provided.

### Web Controllers

Responsible for the Blade interface.

---

### API Controllers

Responsible for the REST API.

Both controllers delegate business logic to Services.

---

## Models

The application contains the following Eloquent models:

- User
- Project
- ProjectDataset
- Audit
- AIConversation
- AIMessage

---

## Services

Business logic is centralized inside Services.

Controllers should remain lightweight.

---

## Form Requests

Every incoming request requiring validation uses a dedicated Form Request.

Examples:

- StoreProjectRequest
- UpdateProjectRequest
- LoginRequest

---

## API Resources

Every API response is formatted using Laravel API Resources.

Examples:

- UserResource
- ProjectResource
- AuditResource

---

## Policies

Authorization rules are implemented using Laravel Policies.

Examples:

- ProjectPolicy
- AuditPolicy
- UserPolicy

---

## Middleware

The application uses middleware for:

- Authentication
- Authorization
- Rate Limiting

---

## Jobs & Queues

Long-running tasks are executed asynchronously.

Examples:

- Audit execution
- AI analysis
- Report generation

---

## Cache

Cache is used for:

- Dashboard statistics
- Frequently accessed metrics

---

## Transactions

Database transactions guarantee data consistency during critical operations.

---

## Pagination

All collection endpoints support pagination.

---

## Rate Limiting

Public API endpoints are protected using Laravel Rate Limiting.

---

# 16. Laravel Concepts Mapping

| Laravel Concept | Usage in FiberFlow            |
| --------------- | ----------------------------- |
| MVC             | Overall architecture          |
| Web Routes      | Blade demonstration interface |
| API Routes      | Main REST API                 |
| Controllers     | HTTP request handling         |
| Migrations      | Database schema               |
| Eloquent ORM    | Data access                   |
| Relationships   | Entity associations           |
| CRUD            | Users and Projects            |
| Form Requests   | Validation                    |
| API Resources   | JSON formatting               |
| Sanctum         | API authentication            |
| Middleware      | Route protection              |
| Policies        | Authorization                 |
| Enums           | Business statuses             |
| Services        | Business logic                |
| Jobs            | Long operations               |
| Queues          | Background processing         |
| Events          | Audit completion              |
| Listeners       | Audit logging                 |
| Transactions    | Data consistency              |
| Cache           | Dashboard performance         |
| Pagination      | API collections               |
| Rate Limiting   | API security                  |
| Factories       | Test data                     |
| Seeders         | Initial data                  |
| Pest            | Automated tests               |
| Laravel AI SDK  | AI Assistant                  |
| Docker          | Containerization              |
| GitHub Actions  | Continuous Integration        |
| Azure           | Deployment                    |
| Supervisor      | Queue workers                 |

# 17. Project Structure

```text
app/
│
├── Enums/
│
├── Events/
│
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   └── Web/
│   │
│   ├── Middleware/
│   │
│   ├── Requests/
│   │
│   └── Resources/
│
├── Jobs/
│
├── Listeners/
│
├── Models/
│
├── Policies/
│
├── Providers/
│
└── Services/

database/
│
├── factories/
├── migrations/
└── seeders/

routes/
│
├── api.php
└── web.php

tests/
│
├── Feature/
└── Unit/
```

---

# 18. Services

The Service layer contains all business logic.

Controllers are responsible only for receiving HTTP requests and returning responses.

---

## ProjectService

### Responsibilities

- Create projects
- Update projects
- Archive projects
- Retrieve project information
- Manage project lifecycle

---

## GISService

### Responsibilities

- Import PostGIS data
- Convert to GeoJSON
- Store ProjectDataset
- Retrieve imported dataset
- Search network elements
- Filter GIS data
- Prepare network information for the API

---

## AuditService

### Responsibilities

- Launch technical audits
- Validate engineering rules
- Calculate project quality score
- Request AI interpretation
- Return audit results

---

## AIService

### Responsibilities

- Communicate with Groq AI
- Interpret audit results
- Generate recommendations
- Explain detected anomalies
- Answer contextual questions

---

## ReportService

### Responsibilities

- Generate PDF reports
- Generate Excel exports
- Prepare downloadable documents

---

## DashboardService

### Responsibilities

- Calculate dashboard KPIs
- Retrieve recent audits
- Compute project statistics
- Cache dashboard information

---

# 19. Jobs

Long-running operations are executed asynchronously using Laravel Jobs.

---

## RunAuditJob

### Responsibilities

- Execute the technical audit
- Analyze GIS data
- Compute audit score

---

## AnalyzeAuditJob

### Responsibilities

- Send audit results to Groq
- Retrieve AI interpretation
- Store AI summary

---

## GenerateReportJob

### Responsibilities

- Generate PDF report
- Generate Excel report
- Prepare download

---

# 20. Events & Listeners

Only one Event/Listener pair is implemented to demonstrate Laravel's event-driven architecture while keeping the project simple.

---

## Event

### AuditCompleted

Triggered when an audit finishes successfully.

---

## Listener

### StoreAuditLogListener

Responsible for recording the completion of the audit in the application logs.

Future versions may include:

- Email notifications
- Real-time notifications
- WebSocket broadcasting

---

# 21. Authentication & Authorization

## Authentication

FiberFlow uses:

- Laravel Sanctum

### Sanctum

Provides Bearer Token authentication for the REST API.

Provides the authentication interface used by the Blade demonstration client.

---

## Authorization

Authorization is handled using Laravel Policies.

Examples:

- UserPolicy
- ProjectPolicy
- AuditPolicy

---

## Roles

### Administrator

Permissions:

- Manage users
- Manage projects
- Launch audits
- View reports
- Access dashboard

---

### Engineer

Permissions:

- View projects
- Launch audits
- View reports
- Use AI Assistant
- Access dashboard

---

# 22. Packages

## Production Packages

| Package                 | Purpose            |
| ----------------------- | ------------------ |
| laravel/sanctum         | API Authentication |
| laravel/ai              | AI Integration     |
| maatwebsite/excel       | Excel Export       |
| barryvdh/laravel-dompdf | PDF Generation     |

---

## Development Packages

| Package                   | Purpose     |
| ------------------------- | ----------- |
| pestphp/pest              | Testing     |
| laravel/telescope         | Debugging   |
| barryvdh/laravel-debugbar | Development |
| laravel/pail              | Log Viewer  |

---

## Frontend

| Package      | Purpose                   |
| ------------ | ------------------------- |
| Blade        | Demonstration Interface   |
| Tailwind CSS | UI Styling                |
| Alpine.js    | Client-side Interactivity |
| Leaflet      | Interactive Map           |
| Chart.js     | Dashboard Charts          |

---

# 23. Testing Strategy

The project uses Pest for automated testing.

---

## Feature Tests

Feature tests verify:

- Authentication
- CRUD operations
- Protected routes
- Validation
- API responses
- Authorization

---

## Unit Tests

Unit tests verify:

- Service classes
- Business logic
- Helper methods
- Enum behavior

---

## Queue Testing

Queues are tested using:

```php
Queue::fake();
```

---

## AI Testing

The AI service is mocked during tests to avoid external API calls.

---

## Test Coverage

The project aims to cover:

- Authentication
- Authorization
- Project CRUD
- Audit execution
- AI integration
- Report generation

# 24. API Strategy

## API First

FiberFlow follows an **API First** approach.

The REST API is developed before the Blade demonstration interface.

Both the API and the web interface share the same Services, ensuring that business logic is implemented only once.

---

## API Architecture

```text
Client

│

├── Blade Interface

│

└── External Client
      │
      ▼

REST API

      │

Controllers

      │

Form Requests

      │

Services

      │

Eloquent Models

      │

PostgreSQL + PostGIS
```

---

## Response Format

All endpoints return JSON responses formatted through Laravel API Resources.

Example:

```json
{
  "data": {
    "id": 1,
    "name": "Agadir Centre",
    "project_type": "Distribution",
    "study_phase": "PRO"
  }
}
```

---

## HTTP Status Codes

| Code | Meaning               |
| ---- | --------------------- |
| 200  | Success               |
| 201  | Resource Created      |
| 202  | Job Accepted          |
| 204  | No Content            |
| 400  | Bad Request           |
| 401  | Unauthorized          |
| 403  | Forbidden             |
| 404  | Not Found             |
| 409  | Conflict              |
| 422  | Validation Error      |
| 429  | Too Many Requests     |
| 500  | Internal Server Error |

---

## API Documentation

The API will be documented using **Laravel Scribe**.

The documentation will include:

- Authentication
- Request examples
- Response examples
- Validation rules
- Error responses

---

# 25. Deployment Architecture

## Development Environment

- Laravel 13
- PHP 8.3
- PostgreSQL
- PostGIS
- Docker Compose

---

## Production Environment

- Ubuntu Server
- Nginx
- PHP-FPM
- PostgreSQL + PostGIS
- Supervisor
- Docker

---

## Continuous Integration

GitHub Actions automatically executes:

- Composer Install
- Laravel Pint (optional)
- Pest Tests

Every push must produce a successful CI check before deployment.

---

## Queue Worker

Supervisor is responsible for keeping Laravel Queue Workers running continuously.

Jobs executed in the background include:

- Technical Audit
- AI Analysis
- Report Generation

---

# 26. Development Roadmap

## Phase 1 — Project Initialization

- Create Laravel project
- Configure Git
- Configure PostgreSQL
- Install Sanctum
- Configure Docker

---

## Phase 2 — Authentication

- Login
- Logout
- Policies
- Middleware
- API Authentication
- Blade Authentication

---

## Phase 3 — User Management

- CRUD Users
- Validation
- Policies
- Resources
- Tests

---

## Phase 4 — Project Management

- CRUD Projects
- Parent Project Management
- Validation
- Resources
- Tests

---

## Phase 5 — Network Consultation

- PostgreSQL/PostGIS connection
- GIS data visualization
- Search
- Filtering
- Pagination

---

## Phase 6 — Technical Audit

- Audit Service
- Quality Score
- Audit Job
- Queue
- Tests

---

## Phase 7 — AI Assistant

- Laravel AI SDK
- Groq Integration
- Structured Output
- Contextual Discussion
- AI Recommendations

---

## Phase 8 — Dashboard

- Statistics
- Recent Audits
- Charts
- Cache

---

## Phase 9 — Reports

- PDF Generation
- Excel Export
- Download Endpoints

---

## Phase 10 — Testing

- Feature Tests
- Unit Tests
- Queue::fake()
- AI Mocking

---

## Phase 11 — Docker

- Dockerfile
- Docker Compose
- Production Configuration

---

## Phase 12 — Continuous Integration

- GitHub Actions
- Automated Tests
- Quality Checks

---

## Phase 13 — Deployment

- Azure Virtual Machine
- Nginx
- Supervisor
- Production Environment

---

## Phase 14 — Documentation

- README
- API Documentation
- MCD
- MLD
- Architecture Diagram
- Presentation Slides

---

# 27. Future Improvements

The following features are intentionally excluded from Version 1 in order to keep the project focused and achievable.

---

## Network Snapshots

Store historical copies of GIS data to compare network evolution between audits.

---

## Rule Engine

Extract engineering validation rules into a dedicated Rule Engine to simplify maintenance and allow configurable audit rules.

---

## AI Enhancements

- Advanced engineering recommendations
- Predictive analysis
- Automatic anomaly classification
- Multi-step reasoning

---

## AI Chat Improvements

- Conversation history
- Project memory
- Suggested prompts
- Citation of engineering rules

---

## Dashboard Enhancements

- Additional KPIs
- Interactive filters
- Geographic statistics
- Trend analysis

---

## Performance

- Redis Cache
- Laravel Horizon
- Optimized queues

---

## Security

- Multi-factor Authentication
- API Keys
- Audit Logs
- Advanced permission management

---

## Deployment

- Continuous Deployment (CD)
- Automatic Docker image deployment
- Monitoring
- Observability
- Health checks

---

## Clients

- Vue.js frontend
- React frontend
- Mobile application
- Public API for third-party integrations

---

# 28. Conclusion

FiberFlow is an API-first platform dedicated to supervising and auditing FTTH engineering studies.

The application relies on GIS data produced in QGIS and stored in PostgreSQL/PostGIS, while keeping all geographic editing outside the application. FiberFlow focuses on project management, technical auditing, AI-assisted interpretation, and report generation.

The project follows Laravel best practices by separating concerns through Controllers, Form Requests, Services, API Resources, Policies, Jobs, and Events, resulting in a clean and maintainable architecture.

The AI Assistant adds real business value by interpreting audit results, explaining engineering anomalies, providing recommendations, and answering contextual questions related to the current audit.

This project also demonstrates the complete backend development lifecycle expected by the Fil Rouge:

- API First Architecture
- Laravel Sanctum
- PostgreSQL + PostGIS
- REST API
- Blade Demonstration Interface
- Form Requests
- API Resources
- Eloquent ORM
- Policies
- Services
- Jobs & Queues
- Events & Listeners
- Laravel AI SDK
- Pest Testing
- Docker
- GitHub Actions
- Azure Deployment
- Supervisor
- Technical Documentation

FiberFlow has been intentionally designed with a clear scope to ensure a high-quality implementation while leaving room for future evolution through additional engineering features, advanced AI capabilities, and enterprise-level scalability.
