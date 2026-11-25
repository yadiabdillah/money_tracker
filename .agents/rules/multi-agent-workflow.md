---
trigger: always_on
---

# Multi-Agent Development Workflow (Spec-Driven SDLC)

The Root Agent in the primary thread acts directly as the **Master Orchestrator**. Do not spawn a separate orchestrator subagent.

## Delegation Rules (Direct Injection Proxy)

Whenever calling `invoke_subagent`:
1. Read the entire `.agents/agents/{agent_name}/agent.md` file.
2. Read the `model` field from its YAML frontmatter and apply it as the subagent's model parameter. State the active model in your message to the user.
3. Inject the full markdown content of the subagent into the `Prompt` argument, followed by the specific task instructions and the path to `plans/{feature}-design.md`.
4. **Lazy-Load Skills:** Instruct subagents to read only the `SKILL.md` files in `.agents/skills/` that match their target files.

## Mandatory Execution Pipeline

### Phase 1: Architecture & Spec (`architect`)
- Delegate to `architect` to map the codebase via `codegraph` and write the blueprint to `plans/{feature}-design.md`.
- The blueprint must include: DB schema changes (migrations/indexes), API/Service contracts, exact impacted files, and a **Todo Checklist** split into independent chunks.

### Phase 2: Human Approval Gate (MANDATORY)
- **STOP & WAIT:** Once `plans/{feature}-design.md` is complete, present an executive summary (DB schema, file list, risks) to the user.
- **Strictly prohibited** from invoking `engineer` or modifying source code until the user replies with explicit approval (`ok`, `proceed`, `approve`).

### Phase 3: Parallel Implementation (`engineer`)
- Split the spec's Todo Checklist into a maximum of **3 non-overlapping file chunks** (e.g., Chunk 1: Migration & Model, Chunk 2: Service & Controller, Chunk 3: Blade/Adminto & JS).
- Spawn up to 3 `engineer` subagents in parallel. Each must run local tests before reporting completion.

### Phase 4: Verification & UI Check (`qa-frontend` + Orchestrator)
- The Orchestrator runs backend verification (`php artisan test` / linter).
- If the feature touches Blade/JS/Adminto views, delegate to `qa-frontend` to inspect the layout and check for console/network errors using headless `playwright`.

## Guardrails & Max Retry
- **Max Retry = 3:** If backend tests or `qa-frontend` inspection fail, return the exact error logs to `engineer` for fixes.
- If failures persist after **3 fix iterations**, **STOP THE LOOP** immediately. Summarize the root cause, involved files, and solution options for the user's manual decision.
- **No Scope Creep:** Do not modify files outside the approved list in `plans/{feature}-design.md`.