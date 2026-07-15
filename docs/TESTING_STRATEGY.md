# Testing Strategy

## FiberFlow

Version 1.0

---

# 1. Purpose

This document defines the testing strategy adopted for the FiberFlow application.

The objective is to ensure that the application's business logic, API endpoints, authentication, authorization, AI integration and asynchronous processing behave correctly.

FiberFlow uses **Pest** as its testing framework.

---

# 2. Testing Objectives

The testing strategy aims to verify:

- Authentication
- Authorization
- CRUD operations
- Business rules
- Validation
- API responses
- Queue dispatching
- AI integration
- Report generation

---

# 3. Test Categories

## Feature Tests

Feature tests verify the complete behavior of the application through HTTP requests.

Covered areas:

- Authentication
- Protected routes
- CRUD endpoints
- Validation
- Policies
- API Resources
- JSON responses

---

## Unit Tests

Unit tests verify isolated business logic.

Covered classes include:

- ProjectService
- GISService
- AuditService
- AIService
- ReportService
- DashboardService

---

# 4. Authentication Tests

The following scenarios must be tested.

## Login

- valid credentials
- invalid credentials

---

## Sanctum

Verify that authenticated users receive a valid Bearer Token.

---

## Protected Routes

Unauthenticated requests must return

```
401 Unauthorized
```

---

# 5. Authorization Tests

Verify role permissions.

## Administrator

Can:

- manage users
- manage projects
- import datasets
- launch audits

---

## Engineer

Can:

- view projects
- import datasets
- launch audits
- use AI Assistant

Cannot:

- manage users

Forbidden operations must return

```
403 Forbidden
```

---

# 6. Validation Tests

Every Form Request must be tested.

Examples:

Project creation

Dataset import

User creation

Login

Expected response

```
422 Unprocessable Entity
```

---

# 7. CRUD Tests

Verify:

Create

Read

Update

Archive

for:

- Users
- Projects

---

# 8. Dataset Tests

Verify:

Dataset import creates a ProjectDataset.

Dataset re-import replaces the previous dataset.

A project cannot have more than one dataset.

An audit cannot start if no dataset exists.

---

# 9. Audit Tests

Verify:

Audit can be launched.

Audit is stored.

Quality score is calculated.

Audit summary is stored.

Recommendations are stored.

---

# 10. Queue Tests

Queue processing must be tested using

```php
Queue::fake();
```

Verify:

RunAuditJob dispatched.

AnalyzeAuditJob dispatched.

GenerateReportJob dispatched.

---

# 11. AI Tests

External AI calls must never be executed during tests.

The AI service should be mocked.

Verify:

Summary stored.

Recommendations stored.

Errors handled correctly.

---

# 12. Report Tests

Verify:

PDF generation.

Excel export.

Correct HTTP responses.

---

# 13. API Response Tests

Verify response structure.

Example

```json
{
  "success": true,
  "message": "...",
  "data": {}
}
```

Verify:

Correct status code.

Correct JSON structure.

Correct resource formatting.

---

# 14. Database Tests

Verify:

Relationships.

Transactions.

Cascade behavior.

Project dataset uniqueness.

Audit persistence.

Conversation persistence.

---

# 15. Seeder Tests

Verify:

Administrator account.

Engineer account.

Demo projects.

Demo dataset.

---

# 16. Factory Tests

Factories should exist for:

- User
- Project
- ProjectDataset
- Audit
- AIConversation
- AIMessage

---

# 17. Performance Tests

Verify:

Pagination.

Dashboard cache.

Large dataset import.

Repeated audit execution.

---

# 18. Test Coverage

The project should cover:

| Module          | Tested |
| --------------- | ------ |
| Authentication  | ✅     |
| Authorization   | ✅     |
| Users           | ✅     |
| Projects        | ✅     |
| Dataset Import  | ✅     |
| Technical Audit | ✅     |
| AI Integration  | ✅     |
| Reports         | ✅     |
| Dashboard       | ✅     |

---

# 19. Continuous Integration

GitHub Actions executes:

- Composer Install
- Laravel Pint (optional)
- Pest Tests

Every push must pass before deployment.

---

# 20. Future Improvements

Future versions may include:

- Performance benchmarking
- Load testing
- Browser testing
- End-to-end testing
- API contract testing
- Static analysis
- Mutation testing
