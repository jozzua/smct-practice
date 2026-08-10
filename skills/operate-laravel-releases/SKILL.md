---
name: operate-laravel-releases
description: Prepare, execute, audit, or document safe Laravel releases for this repository, including immutable release selection, QA evidence intake, operational risk review, backup and restore readiness, staging validation, production approval gates, migrations, configuration caching, queue reloads, health and smoke checks, observation, rollback decisions, and redacted handoff evidence. Use for release plans, deployment runbooks, production deployment requests, release verification, rollback planning, or post-deployment audits; do not use to design the CI/QA pipeline itself.
---

# Operate Laravel Releases

Promote one immutable, QA-approved artifact through a controlled release. Keep planning
and auditing read-only; access or change production only after explicit authorization
for the exact target, release, scope, and action.

## 1. Establish the boundary

Read `AGENTS.md`, `docs/kanban.md`, deployment documentation, the working tree, and
the relevant environment and process configuration. Inspect the repository's current
state before implementation work and follow its Kanban workflow.

Consume evidence produced by `$build-laravel-qa-pipeline`; do not duplicate or redesign
that skill's CI, test, browser-automation, or branch-protection work. Require:

- an exact commit SHA and, when available, an immutable artifact or image digest;
- passing required checks tied to that SHA;
- reviewer approval and resolved release-blocking findings;
- an inventory of changes, migrations, dependencies, configuration, queues, scheduled
  work, and customer-visible behavior;
- the intended environment, release window, operator, approver, and rollback owner.

Reject mutable labels such as a branch name, `latest`, or an unpinned build as the
release candidate. Do not rebuild or modify the artifact between staging and production.

## 2. Select the operating mode

Choose one mode and state it in the release record:

- **Plan or audit:** inspect repository and supplied evidence without production access.
- **Staging:** act only on the confirmed staging target and record results.
- **Production:** proceed only after explicit authorization identifies the production
  target, exact release candidate, approved actions, and release window.

Treat missing production authorization as a hard stop, not implied consent. Never use
production credentials merely because they are available. Pause for renewed approval
when the target, release candidate, migration scope, live-data impact, downtime, or
rollback plan differs materially from what was approved.

## 3. Review release risk

Classify risk before staging:

- schema compatibility and migration duration, locks, backfills, and reversibility;
- configuration or secret additions, removals, and cache effects;
- `APP_NAME` changes and their session, cache, and Redis namespace impact;
- queue payload compatibility and long-running worker behavior;
- scheduled jobs, cache warmups, search indexes, storage, mail, and external services;
- frontend/backend compatibility during a rolling or multi-host deployment;
- customer-visible downtime, authentication, checkout, order, and data risks.

Require an expand-and-contract plan for destructive or incompatible schema changes.
Separate long backfills from request-path deployments. Do not accept "migration rollback"
as the sole recovery plan when a down migration would lose data or fail under the new
application version.

Preserve `SESSION_COOKIE`, `CACHE_PREFIX`, and `REDIS_PREFIX`; do not let a branding or
`APP_NAME` change alter them as a side effect. Never regenerate or replace `APP_KEY` in
an existing environment.

## 4. Prove recovery readiness

Record before production:

- the exact application rollback target and how to select it;
- database backup or snapshot identifier, completion time, retention, and responsible
  operator;
- restore procedure, most recent restore-test evidence, expected recovery time, and
  acceptable data-loss window;
- the treatment of migrations, queued jobs, uploaded files, caches, and external side
  effects during rollback;
- stop criteria and the person authorized to choose rollback or roll-forward.

Do not call a backup usable solely because it exists. Escalate missing or stale restore
evidence according to the approved risk policy; do not invent recovery guarantees.

## 5. Validate in staging

Deploy the same immutable candidate with the production procedure and production-like
configuration. Confirm:

1. migrations finish and the expected migration state is recorded;
2. configuration and route/view/event caches build successfully where used;
3. queue workers reload and resume processing;
4. application liveness, dependency readiness, and critical smoke checks pass;
5. logs and metrics remain healthy for the agreed observation period;
6. the rollback procedure or a safe representative recovery step is rehearsed.

Record exceptions between staging and production. Treat an unexplained difference as a
release risk requiring review, not as evidence that production will behave identically.

## 6. Run production preflight and gate

Immediately before the gate, confirm the target identity without exposing credentials,
the current deployed SHA, available capacity and disk space, database connectivity,
backup evidence, maintenance-mode plan, queue process manager, health endpoints,
observation dashboards, and rollback target. Verify required configuration keys by name
and presence only; never print secret values.

Present a concise go/no-go packet containing the candidate SHA or digest, QA and staging
results, risk summary, migration and downtime impact, recovery evidence, smoke plan,
observation window, rollback triggers, and accountable operators. Obtain the protected
environment approval or equivalent human authorization. Record who approved and when,
without storing authentication material.

## 7. Deploy deterministically

Use the repository's documented deployment mechanism and deployment lock. Do not invent
provider-specific commands when the hosting platform is unknown. Keep an append-only,
timestamped command and result record with secrets redacted.

Execute only the approved sequence:

1. verify the target and candidate again;
2. enable maintenance handling only if the approved plan requires it;
3. install or activate the immutable release without editing it in place;
4. apply configuration changes through the environment's secret/configuration system;
5. run approved migrations safely;
6. clear stale Laravel optimization state, then rebuild only the caches supported by the
   release and environment;
7. reload long-running processes and queue workers;
8. restore traffic or leave maintenance mode;
9. run verification and observation.

Avoid concurrent production deploys. If execution partially fails, stop, preserve the
evidence, determine the actual live state, and use the approved recovery decision rather
than blindly rerunning every command.

## 8. Handle migrations and live data safely

Inspect every pending migration and map it to its expected database effect. Use Laravel's
production confirmation override such as `migrate --force` only when that command and
target were explicitly approved. Use migration isolation when supported, but verify the
post-run migration state: a lock-protected command may exit successfully after another
operator performed the work.

Never run seeders as a production data-migration mechanism. For existing live records:

- identify records by primary key or a confirmed stable unique identifier;
- state exact intended before/after values and customer impact;
- use an idempotent data migration or narrowly scoped maintenance command;
- obtain explicit approval before execution;
- verify the intended records by stable identifier afterward.

Never use a broad or blind `migrate:rollback`. Prefer roll-forward when rollback would be
destructive or when the old application cannot safely consume the new schema.

## 9. Rebuild caches and reload workers

Run `php artisan optimize:clear` before rebuilding deployment caches when required by this
repository. Build `config:cache` only after the real environment values are correct;
remember that `.env.example` and `config/app.php` defaults do not override an existing
environment value. Laravel 13's `optimize:clear` can also remove keys from the default
cache store, so document and approve the expected cache and customer impact instead of
treating it as harmless routine cleanup. Treat a cache command's success as insufficient
until the running application resolves the expected non-secret configuration.

Reload queue workers with the configured process manager or platform mechanism. Account
for in-flight jobs and mixed-version serialized payloads. Verify replacement workers are
running, consuming queues, and not accumulating failures. Include schedulers, Horizon,
Octane, Reverb, or other long-lived processes only when the repository actually uses them.

## 10. Verify and observe

Treat Laravel's `/up` response as a boot/liveness signal only. Verify dependency readiness
separately, including the database, cache, queue, storage, and required external services.

Run a small, predefined smoke set against the live entry point. Cover the document title
and brand, catalog browsing, authentication boundary, cart/checkout availability, and
order access as applicable. Keep production smoke tests read-only unless synthetic data,
side effects, and cleanup were explicitly approved.

Confirm the served release SHA or digest, migration state, response health, worker health,
error rate, latency, queue depth/failures, and critical business indicators. Observe for
the approved period and compare with the recorded baseline. Redact personal data, tokens,
cookies, session payloads, credentials, and secret configuration from all evidence.

## 11. Decide recovery and hand off

Apply the agreed stop criteria. Roll back or roll forward according to schema and data
compatibility, not convenience. Obtain renewed authorization for recovery actions outside
the approved plan. After recovery, rerun the same health and smoke checks and record the
actual final state.

Produce a redacted handoff containing:

- environment, candidate SHA/digest, previous and final release, and timestamps;
- approvals, operators, deployment mechanism, and executed steps;
- QA, staging, backup/restore, migration, health, smoke, worker, and observation evidence;
- deviations, incidents, customer impact, and follow-up owners;
- rollback decision and result, or the retained rollback target and expiry;
- verification that no production seeder ran, `APP_KEY` was preserved, and operational
  session/cache/Redis identifiers remained unchanged.

Never claim completion from Git history, CI, or `/up` alone. Mark the release successful
only when the target serves the intended immutable candidate and all required production
verification passes.
