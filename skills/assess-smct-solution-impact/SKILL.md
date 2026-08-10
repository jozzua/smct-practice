---
name: assess-smct-solution-impact
description: Assess proposed or partially implemented SMCT changes by tracing requirements to repository evidence, identifying cross-cutting architecture effects, and recommending the smallest reversible design with verification, rollout, and rollback guidance. Use for solution-design reviews, PRD impact assessments, implementation-readiness checks, architecture trade-offs, requirement-to-code traceability, partial or conflicting feature audits, and ADR recommendations in the Maligaya Trading Company Laravel rehearsal repository.
---

# Assess SMCT Solution Impact

Evaluate the current system before proposing a change. Separate repository facts from inferences and recommendations. Remain read-only unless the user explicitly asks to implement the design or update documentation.

## Establish Boundaries

1. Read `AGENTS.md` completely. Inspect `docs/kanban.md`, `docs/development-log.md`, `docs/rehearsal-learning-record.md`, and the relevant PRD or task description.
2. Inspect the working tree and recent history before trusting narrative documentation:

   ```bash
   git status --short --branch
   git log -12 --date=iso-local --format='%h %ad %s'
   git diff --stat
   git diff --check
   ```

3. Preserve unrelated rehearsal work. Distinguish committed behavior, tracked working-tree changes, ignored local state, and stale documentation.
4. Do not modify code, documentation, Kanban state, data, or infrastructure during an assessment. If implementation is requested, finish the assessment first and follow the repository workflow as a separate phase.
5. Never access production infrastructure or change production data without explicit authorization. Treat `APP_NAME`, session cookies, cache prefixes, Redis prefixes, and existing database records as operational concerns, not incidental implementation details.

## Build the Evidence Map

1. Decompose the request into atomic, testable acceptance criteria. Preserve exact thresholds, boundary conditions, identifiers, roles, states, and customer-visible outcomes.
2. Trace each criterion through the relevant Laravel surfaces:

   - routes and middleware;
   - request validation and authorization;
   - controllers, models, enums, and domain rules;
   - migrations, seeders, factories, and persisted snapshots;
   - Blade views, shared components, and frontend assets;
   - configuration and environment-backed behavior;
   - focused tests, full-suite coverage, and build checks;
   - logs, deployment steps, monitoring signals, and project documentation.

3. Search by business concepts and concrete symbols. For commerce changes, follow the complete flow from catalog selection through pricing, cart or checkout, VAT, delivery thresholds, order persistence, and order display. Identify products by SKU or another stable identifier, never by display name.
4. Inspect actual code and tests before concluding that a documented Todo is missing. A feature can be partially implemented while its PRD, user messaging, boundary coverage, or operational handling remains incomplete.
5. Assign exactly one status to every criterion:

   - **Implemented** — code and adequate verification satisfy it.
   - **Partial** — some behavior exists, but a required path or proof is absent.
   - **Missing** — no supporting implementation was found.
   - **Conflicting** — sources or behaviors disagree.
   - **Stale** — documentation describes a superseded state.
   - **Unknown** — available evidence cannot establish the result.

6. Cite repository paths and symbols for each finding. Include line numbers when useful. Label statements as **Observed**, **Inferred**, or **Recommended**; do not present assumptions as facts.

## Assess Architecture Impact

Evaluate only dimensions relevant to the request, but explicitly consider:

- ownership of business rules and avoidance of duplicated logic;
- data shape, invariants, migrations, seed behavior, and existing-record handling;
- API, route, view, and backward-compatibility effects;
- authentication, authorization, validation, privacy, and abuse cases;
- pricing consistency, integer-centavo arithmetic, VAT, delivery, and stored order-item prices;
- query count, indexing, latency, caching, queues, and failure modes;
- observability, support diagnostics, and safe handling of logs;
- deployment order, configuration changes, session/cache effects, and rollback;
- accessibility and customer-visible states;
- testability, restore points, Kanban accuracy, and rehearsal learning records.

State when a dimension is not applicable. Do not invent scalability, security, or operational requirements unsupported by the request; identify them as questions or assumptions instead.

## Recommend the Design

1. Define the smallest coherent change that satisfies the acceptance criteria and keeps one source of truth for each business rule.
2. Compare credible alternatives when the choice affects multiple components, introduces a lasting constraint, or is costly to reverse. For each alternative, state benefits, costs, risks, and why it was accepted or rejected.
3. Recommend an ADR only when the decision is cross-cutting, establishes a durable convention, changes data or integration boundaries, has meaningful alternatives, or is difficult to reverse. Otherwise record the rationale in the implementation or learning documentation.
4. Prefer small, reversible increments. Specify compatibility strategy, migration or backfill needs, feature controls when justified, deployment sequence, observable success signals, failure signals, and rollback steps.
5. For existing production records, propose an idempotent data migration or narrowly scoped maintenance command keyed by a confirmed stable identifier. State intended before/after values and require approval before execution. Do not use seeders as a production migration mechanism.

## Define Verification

Tie every proposed check to an acceptance criterion or risk. Include:

- focused tests for happy paths, negative paths, exact boundaries, and persisted values;
- regression tests for shared business rules and representative unaffected records;
- authorization, validation, query-count, or accessibility checks when applicable;
- `composer test` for PHP or backend changes and `npm run build` for Blade, CSS, or JavaScript changes when practical;
- browser verification for customer-visible behavior;
- deployment verification and rollback validation for environment, schema, or data changes.

Do not claim a criterion is verified merely because a related test exists. Distinguish tests inspected, tests run, and checks not run.

## Deliver the Assessment

Return a concise architecture review in this order:

1. **Decision summary** — recommended design, scope, and readiness.
2. **Current-state evidence** — relevant flow, sources inspected, and worktree context.
3. **Traceability matrix** — criterion, status, evidence, gap, and proposed verification.
4. **Impact assessment** — affected components, data, security, performance, operations, UX, and documentation.
5. **Alternatives and rationale** — include an ADR recommendation when warranted.
6. **Delivery plan** — incremental implementation order, rollout, observability, and rollback.
7. **Risks and open questions** — distinguish blockers from non-blocking assumptions.

Keep the answer proportional to the change. Surface contradictions directly. Do not mark Kanban work complete or imply production readiness when required verification is missing.
