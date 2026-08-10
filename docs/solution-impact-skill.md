# Solution-impact skill

`$assess-smct-solution-impact` is the repository's architecture-review workflow. It
turns a proposed feature, PRD, or cross-cutting change into an evidence-backed
assessment before implementation begins.

## When to use it

Use the skill to:

- reconcile requirements with the code and tests that already exist;
- assess changes spanning several boundaries, such as catalog, checkout, VAT,
  shipping, persistence, configuration, security, or deployment;
- compare viable designs and record why one is preferred; or
- define migration, rollout, rollback, observability, and acceptance needs.

Do not use it for a general repository briefing, a routine product-photo swap, or a
known sale-pricing implementation. It does not implement a feature, mutate data, or
access production unless the user separately and explicitly authorizes that work.

## Evidence-first workflow

1. Read the request and split its requirements into atomic acceptance criteria.
2. Inspect current Git state, the Kanban board, applicable PRDs, routes, domain
   logic, persistence, views, tests, configuration, operations, and documentation.
3. Classify every criterion as **implemented**, **partial**, **missing**,
   **conflicting**, **stale**, or **unknown**, citing repository evidence.
4. Trace affected boundaries and non-functional concerns, including compatibility,
   security, data integrity, performance, observability, and operations.
5. Compare realistic alternatives and recommend the smallest reversible design.
6. Define verification, rollout, rollback, and any decision record that is warranted.

Facts, assumptions, and recommendations must remain visibly separate. Missing
evidence is reported as unknown rather than guessed.

## Output contract

An assessment contains:

1. current-state evidence;
2. a requirement traceability matrix;
3. the proposed design and affected data flow;
4. alternatives and decision rationale;
5. risks and non-functional requirements;
6. deployment, migration, rollback, and observability notes;
7. a test and acceptance plan; and
8. discrepancies among code, tests, PRDs, Kanban, and other documentation.

Create or recommend an architecture decision record only for a significant,
cross-cutting, or difficult-to-reverse choice.

## Guardrails

- Stay read-only by default and distinguish analysis from authorization to change.
- Use stable identifiers and current repository evidence; do not treat seeders as
  production migrations or narrative documentation as proof of runtime behavior.
- Preserve operational identifiers, rehearsal restore points, unrelated working-tree
  changes, and the Kanban workflow.
- State uncertainty, avoid invented production facts, and never claim deployment or
  live verification from local evidence alone.

## Example

> Use `$assess-smct-solution-impact` to review the free-shipping PRD, identify what
> is implemented or partial, and recommend the safest remaining design with rollout,
> rollback, and acceptance tests.

## Relationship to other repository skills

- `$get-smct-context` answers “what is happening in this repository?”; use it for a
  general briefing or handoff rather than a focused architecture decision.
- `$swap-product-card-photo` executes the established SKU-based image workflow; an
  architecture assessment is unnecessary unless the underlying image design is
  being reconsidered.
- `$apply-product-sale-pricing` executes the established pricing workflow; use the
  solution-impact skill first only when requirements or system boundaries are
  unclear, disputed, or changing.
