# Ruleset — MCP Workflow & AI Agent Rules

> **IMPORTANT**: These rules are strict and mandatory. The AI agent MUST follow every rule below without exception. Any deviation must be explicitly approved by the user.

---

## 1. MCP Servers Configuration

This project uses 3 MCP servers defined in `opencode.json`:

| Server | Type | Purpose |
|--------|------|---------|
| `laravel-boost` | Local (`php artisan boost:mcp`) | Laravel framework assistance |
| `github` | Local (`npx @modelcontextprotocol/server-github`) | GitHub operations (branches, commits, PRs) |
| `jira` | Remote (`npx mcp-remote https://mcp.atlassian.com/v1/mcp/authv2`) | Atlassian Rovo MCP — Jira issue management via OAuth |

### 1.1 Jira MCP (Atlassian Rovo)

- Uses OAuth 2.1 browser authentication via `mcp-remote`
- Credentials cached in `~/.mcp-auth` — no env vars needed
- Project key: **TA** (TalentMatch)
- Available issue types:
  - `Tâche` (Task) — main feature tickets
  - `Sous-tâche` (Subtask) — subtasks of a Task
  - `Epic` — collection of tasks
  - `Idée`, `Élément`, `Demande` — other types

### 1.2 GitHub MCP

- Uses `@modelcontextprotocol/server-github` via npx
- Requires `GITHUB_PERSONAL_ACCESS_TOKEN` system env var
- Repository: `BEN-ESSAHRAOUI-Yassine/talentmatch`

---

## 2. GIT WORKFLOW RULES — STRICT ENFORCEMENT

### 2.1 AL Branching Strategy

```
main          ← YOU manage this branch (protected)
  └── featureAI/{kebab-case-title}    ← AI agent works here
```

### 2.2 Absolute Prohibitions

1. **NEVER push to `main` branch.** The user is the sole manager of `main`.
2. **NEVER attempt to merge branches directly.**
3. **NEVER force-push.**
4. **ALWAYS work on a `featureAI/*` branch.**

### 2.3 Branch Naming Convention

Format: `featureAI/{kebab-case-title}`
- Lowercase kebab-case
- Based on the Jira ticket title
- Examples:
  - `featureAI/add-authentication-module`
  - `featureAI/cv-parser-integration`
  - `featureAI/fix-rate-limit-bug`

### 2.4 Commit Rules

- Use descriptive commit messages (conventional commits preferred)
- Commit to the feature branch only
- Push to the feature branch on GitHub after each meaningful milestone

### 2.5 Pull Request

- After implementation is complete, create a PR targeting `main`
- The agent creates the PR, the user reviews and merges
- The PR title must match the Jira ticket title

---

## 3. JIRA WORKFLOW RULES

### 3.1 Project & Issue Types

- **Project**: TA (TalentMatch — Assistant IA de Présélection RH)
- **Main issue type**: `Tâche` (id: 10360) — for each feature/change
- **Subtask type**: `Sous-tâche` (id: 10359) — for individual implementation steps

### 3.2 Available Statuses & Transitions

| Status | Transition ID | Description |
|--------|---------------|-------------|
| À faire | — | Default state for new tickets |
| En cours | `21` | Work has started |
| UNDER_REVIEW | `31` | Implementation complete, awaiting review |
| TERMINE | `41` | Done, archived via OpenSpec |

### 3.3 Ticket Management Rules

1. All major feature tickets are pre-created in the **TA** project with status **À faire** at the start
2. The agent NEVER creates new top-level tickets without user approval
3. The agent updates existing tickets (adds subtasks, changes status, updates description)
4. Each feature ticket has:
   - Clear title matching the feature
   - Description with context/goals
   - Subtasks representing individual implementation steps
5. **Subtasks from spec are MANDATORY**: When a spec is generated via `openspec-propose`, the agent MUST create one Jira Subtask (type `Sous-tâche`) for EVERY task listed in the spec. Subtask titles must match the spec task titles exactly. This ensures Jira reflects 100% of the work scope.

---

## 4. OPEnS WORKFLOW — THE COMPLETE CYCLE

This is the **one true workflow** every change must follow.

### Phase 1: Pre-Setup (One-Time, Project Start)

All major feature tickets are created upfront in Jira (TA project, type Tâche, status À faire).

### Phase 2: Per-Change Cycle

```
┌─────────────────────────────────────────────────────┐
│ 1. User says "Work on TA-X"                          │
├─────────────────────────────────────────────────────┤
│ 2. AI agent runs: skill openspec-propose             │
│    • Generates detailed spec (design, UI, tasks)     │
│    • User approves the proposal                      │
├─────────────────────────────────────────────────────┤
│ 3. AI agent updates Jira ticket TA-X:               │
│    • Adds/updates description from spec              │
│    • Creates subtasks from spec tasks                │
│      ⚠️ EVERY task in the spec MUST become a         │
│         Jira Subtask (type: Sous-tâche)               │
│      ⚠️ Subtask titles = spec task titles             │
│    • Transitions TA-X to status "En cours" (id: 21)  │
├─────────────────────────────────────────────────────┤
│ 4. AI agent creates GitHub branch:                  │
│    featureAI/{kebab-case-title}                      │
├─────────────────────────────────────────────────────┤
│ 5. IMPLEMENTATION PHASE:                            │
│    • Write code                                     │
│    • Write/update tests                             │
│    • Run tests to verify                            │
│    • Run linting/formatting                         │
│    • Commit to feature branch                       │
├─────────────────────────────────────────────────────┤
│ 6. AI agent runs: skill openspec-archive            │
│    • Archives the change                            │
│    • Syncs delta specs if needed                    │
├─────────────────────────────────────────────────────┤
│ 7. AI agent transitions Jira ticket:               │
│    TA-X → status "TERMINE" (transition id: 41)      │
├─────────────────────────────────────────────────────┤
│ 8. AI agent pushes feature branch to GitHub         │
│ 9. AI agent creates PR targeting main                │
└─────────────────────────────────────────────────────┘
```

### Phase 3: User Review

10. User reviews the PR on GitHub
11. User merges to `main` (the ONLY way changes reach main)

---

## 5. JIRA TOOL RULES (for the AI agent)

### 5.1 Creating Issues

Use `createJiraIssue` with project `TA`, issueType `Tâche` (id: 10360).
Only pre-create tickets, never create on-the-fly without approval.

### 5.2 Adding Subtasks

Use `createJiraIssue` with:
- project: `TA`
- issueType: `Sous-tâche` (id: 10359)
- parent: the parent ticket key (e.g., `TA-5`)
- summary: brief subtask description

### 5.3 Transitioning Status

Use `transitionJiraIssue` with:
- To start work: transition id `21` (En cours)
- After archive: transition id `41` (TERMINE)

### 5.4 Updating Ticket Fields

Use `editJiraIssue` to update description, summary, or other fields.

---

## 6. GITHUB TOOL RULES (for the AI agent)

### 6.1 Creating Branches

Use `github_create_branch` with:
- owner: `BEN-ESSAHRAOUI-Yassine`
- repo: `talentmatch`
- branch: `featureAI/{kebab-case-title}`
- from_branch: `main`

### 6.2 Committing & Pushing

Use `github_push_files` or local `git add`/`git commit`/`git push` targeting the feature branch ONLY.

### 6.3 Creating PRs

Use `github_create_pull_request` with:
- head: `featureAI/{kebab-case-title}`
- base: `main`
- title matching Jira ticket title
- body summarizing the change

---

## 7. REMINDERS

- Read `AGENTS.md` and `openspec/config.yaml` before any work
- Check current branch with `git branch --show-current` before any git operation
- Never assume you're on the right branch — verify first
- When in doubt about a Jira operation, use `jira_getIssueTypeMetaWithFields` to check available fields
- The user manages `main` — your work ends at the PR
