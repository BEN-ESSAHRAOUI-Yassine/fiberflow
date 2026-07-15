# API Endpoints

## FiberFlow

Version 1.0

---

# 1. Overview

FiberFlow follows an **API First** architecture.

The REST API is the application's primary interface.

All responses are returned as JSON and formatted using Laravel API Resources.

Authentication is handled with Laravel Sanctum using Bearer Tokens.

---

# 2. Authentication

## Login

### POST

```
/api/login
```

Authentication using email and password.

### Request

```json
{
  "email": "engineer@example.com",
  "password": "password"
}
```

### Response

```json
{
  "token": "...",
  "user": {}
}
```

---

## Logout

### POST

```
/api/logout
```

Authentication required.

---

## Current User

### GET

```
/api/user
```

Returns authenticated user information.

---

# 3. Users

(Admin only)

---

## List Users

### GET

```
/api/users
```

---

## Show User

### GET

```
/api/users/{id}
```

---

## Create User

### POST

```
/api/users
```

---

## Update User

### PUT

```
/api/users/{id}
```

---

## Archive User

### DELETE

```
/api/users/{id}
```

---

# 4. Projects

---

## List Projects

### GET

```
/api/projects
```

Supports:

- Pagination
- Search
- Filters

---

## Show Project

### GET

```
/api/projects/{id}
```

---

## Create Project

### POST

```
/api/projects
```

---

## Update Project

### PUT

```
/api/projects/{id}
```

---

## Archive Project

### DELETE

```
/api/projects/{id}
```

---

# 5. Project Dataset

The dataset represents the imported GeoJSON used during technical audits.

---

## Import Dataset

### POST

```
/api/projects/{project}/dataset/import
```

Imports the GIS data from PostgreSQL/PostGIS.

Creates or updates the project's dataset.

---

## Show Dataset Information

### GET

```
/api/projects/{project}/dataset
```

Returns dataset metadata.

The GeoJSON itself is not returned by default.

---

## Re-import Dataset

### POST

```
/api/projects/{project}/dataset/reimport
```

Replaces the existing imported dataset.

---

# 6. Technical Audits

---

## Launch Audit

### POST

```
/api/projects/{project}/audits
```

Dispatches a queue job.

Returns:

```
202 Accepted
```

---

## List Project Audits

### GET

```
/api/projects/{project}/audits
```

---

## Show Audit

### GET

```
/api/audits/{audit}
```

Returns:

- Score
- Statistics
- AI Summary
- Recommendations

---

# 7. AI Assistant

---

## AI Chat

### POST

```
/api/ai/chat
```

Allows the engineer to ask contextual questions.

Example:

```json
{
  "project_id": 3,
  "audit_id": 12,
  "message": "Why is this BO considered isolated?"
}
```

---

## Conversation History

### GET

```
/api/ai/conversations/{conversation}
```

Returns previous messages.

---

# 8. Dashboard

---

## Dashboard Statistics

### GET

```
/api/dashboard
```

Returns:

- Projects
- Audits
- Average Quality Score
- Recent Activity

---

# 9. Reports

---

## Generate PDF

### GET

```
/api/audits/{audit}/report/pdf
```

Downloads the audit report.

---

## Export Excel

### GET

```
/api/audits/{audit}/report/excel
```

Downloads the audit statistics.

---

# 10. HTTP Status Codes

| Code | Meaning               |
| ---- | --------------------- |
| 200  | Success               |
| 201  | Created               |
| 202  | Accepted (Queue Job)  |
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

# 11. Authentication

The following endpoints require authentication:

- All project endpoints
- Dataset endpoints
- Audit endpoints
- AI endpoints
- Dashboard endpoints
- Report endpoints

Authentication method:

```
Authorization: Bearer {token}
```

---

# 12. Authorization

## Administrator

Can:

- Manage users
- Manage projects
- Import datasets
- Launch audits
- View reports

---

## Engineer

Can:

- View projects
- View datasets
- Launch audits
- Use AI Assistant
- View reports
- View dashboard

---

# 13. API Response Standard

Successful responses

```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {}
}
```

Validation errors

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {}
}
```

Unexpected errors

```json
{
  "success": false,
  "message": "An unexpected error occurred."
}
```

---

# 14. Versioning

Current API version:

```
v1
```

Example:

```
/api/v1/projects
```

Future versions should be introduced without breaking backward compatibility.

---

# 15. Future Endpoints

These endpoints are intentionally excluded from Version 1.

- Compare audits
- Dataset version history
- Network snapshots
- Advanced AI analysis
- Notifications
- Public API
- Webhooks
