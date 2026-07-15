# AI Specification

## FiberFlow

Version 1.0

---

# 1. Purpose

The Artificial Intelligence module assists FTTH engineers during technical audits.

It does **not** replace engineering expertise.

Its objective is to analyze audit results, explain detected anomalies, answer contextual questions and suggest possible improvements.

The AI never modifies project data.

---

# 2. AI Objectives

The AI module provides four main capabilities:

- Explain detected anomalies
- Suggest corrective actions
- Summarize technical audits
- Answer contextual questions about the current project

---

# 3. AI Provider

| Item     | Value           |
| -------- | --------------- |
| SDK      | Laravel AI SDK  |
| Provider | Groq            |
| Model    | Configurable    |
| Output   | Structured JSON |

---

# 4. AI Workflow

```text
Engineer

    │

Launch Audit

    │

RunAuditJob

    │

AuditService

    │

Read Project Dataset

    │

Engineering Validation

    │

Generate Audit Summary

    │

AIService

    │

Groq API

    │

Structured Output

    │

Store Results

    │

Display Report
```

---

# 5. AI Input

The AI receives structured information instead of raw GIS data.

Example:

```json
{
  "project": {
    "type": "Distribution",
    "phase": "PRO"
  },
  "statistics": {
    "routes": 25,
    "pbo": 140,
    "bo": 32
  },
  "anomalies": ["...", "...", "..."]
}
```

The AI **does not communicate directly with PostgreSQL/PostGIS**.

It consumes the imported GeoJSON dataset and the audit results prepared by the application.

---

# 6. AI Output

The AI returns structured data.

Example:

```json
{
  "summary": "...",
  "quality": "...",
  "recommendations": ["...", "...", "..."]
}
```

The output is validated before being stored.

---

# 7. Structured Output

The AI must always return a predictable structure.

Required fields:

- summary
- recommendations

Optional fields:

- observations
- risks

This guarantees compatibility with the application.

---

# 8. AI Features

## Audit Interpretation

The AI explains the technical audit in natural language.

---

## Recommendation Generation

The AI proposes engineering improvements based on detected anomalies.

---

## Contextual Chat

Engineers can ask questions related to:

- Current project
- Imported dataset
- Current audit
- Detected anomalies

Example:

> Why is this BO considered isolated?

---

## Audit Summary

Generate a concise explanation suitable for reports.

---

# 9. AI Limitations

The AI cannot:

- Modify GIS data
- Modify projects
- Execute engineering operations
- Create routes
- Edit infrastructure
- Replace engineering expertise

The engineer remains responsible for all technical decisions.

---

# 10. Prompt Strategy

The prompt sent to the model contains:

- Project information
- Study phase
- Audit statistics
- Detected anomalies
- User question (for chat)

Prompt construction is handled exclusively by the AIService.

---

# 11. Conversation Management

Every conversation belongs to:

- one project

Optionally:

- one audit

Messages are stored to preserve discussion history.

---

# 12. Error Handling

If the AI request fails:

- the audit remains valid
- the failure is logged
- the user receives an informative message
- the audit can be analyzed again later

The application must continue functioning even if the AI service is unavailable.

---

# 13. Security

The AI never receives:

- User passwords
- Authentication tokens
- Sensitive application configuration
- Environment variables

Only business data required for analysis is transmitted.

---

# 14. Testing Strategy

During automated tests:

- AI requests are mocked
- No external API calls are performed
- Structured responses are simulated

This ensures deterministic and fast tests.

---

# 15. Future Improvements

The following features are excluded from Version 1:

- Multi-turn engineering reasoning
- Predictive maintenance
- Network optimization suggestions
- Automatic anomaly classification
- RAG using engineering documentation
- AI confidence scoring
- Multi-model support
- Streaming responses
