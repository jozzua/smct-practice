# Operating Laravel Releases

Use the repo-scoped `$operate-laravel-releases` skill when preparing, executing,
auditing, or documenting an SMCT release. It turns an already-reviewed commit into
a controlled deployment with an explicit recovery path and redacted evidence.
The executable workflow is defined in the
[`SKILL.md`](../skills/operate-laravel-releases/SKILL.md) source.

The skill is an operator runbook, not deployment authorization. It must not access
or change production until the user explicitly approves the exact environment and
action.

## Boundary with QA

`$build-laravel-qa-pipeline` defines automated CI checks and deployment gates.
`$operate-laravel-releases` starts from an immutable commit that has passed those
gates and covers release risk review, controlled promotion, runtime operations,
verification, rollback, and evidence. A release must not reinterpret a failed or
missing required check as successful.

## Required inputs

Before producing an executable runbook, confirm:

- target environment, hosting platform, topology, and deployment mechanism;
- exact commit or immutable artifact and its successful required checks;
- production approver, maintenance-window expectations, and deployment locking;
- database engine, backup/restore owner, recovery point, and recovery-time policy;
- queue backend and process manager, scheduler ownership, and cache/session stores;
- staging availability, observability tools, alert ownership, and observation window;
- approved health, smoke, rollback, and customer-communication procedures.

When any platform-specific input is unknown, record it as unresolved and stop before
inventing commands or touching production.

## Release lifecycle

### 1. Prepare

- Identify the exact commit or artifact, release owner, approver, change window, and
  previous known-good release.
- Confirm required QA passed and review changes to migrations, dependencies,
  configuration, queues, caches, scheduled work, and customer-visible behavior.
- Confirm staging results, backup/restore readiness, rollback triggers, rollback
  ownership, and the monitoring window.
- Record expected environment-variable changes by name and effect, never by secret
  value. Never regenerate `APP_KEY` during a release.
- Preserve `SESSION_COOKIE`, `CACHE_PREFIX`, and `REDIS_PREFIX` unless their user
  impact has been explained and separately approved.

### 2. Approve

Present the production plan, exact target, customer impact, migration risk,
verification steps, rollback target, and evidence location. Obtain explicit approval
before any production access or mutation. A rehearsal, staging approval, or approval
of the code change does not authorize production execution.

### 3. Deploy

- Acquire the platform's deployment lock and promote only the approved immutable
  artifact.
- Apply approved environment changes through the platform's secret/configuration
  mechanism; do not print or copy secret values into logs or release notes.
- Run only reviewed migration and runtime commands, in the documented platform
  order. Use maintenance mode only when its customer impact and recovery path are
  approved.
- Reload configuration, application processes, scheduler ownership, and queue
  workers according to the confirmed topology. Verify replacement queue workers
  actually become healthy.

### 4. Verify and observe

- Confirm the deployed commit or artifact and migration state.
- Treat Laravel's `/up` endpoint as application boot/liveness evidence only. Verify
  database connectivity, queues, cache, mail or external dependencies, and critical
  storefront behavior separately.
- Run approved smoke checks for the document title and brand, catalog, sign-in,
  cart/checkout boundaries, and order access. Keep production smoke checks read-only
  unless synthetic records and their cleanup were explicitly approved.
- Review metrics, alerts, failed jobs, and sanitized logs throughout the agreed
  observation window. Do not expose tokens, cookies, passwords, personal data,
  session payloads, connection strings, or full request bodies.

### 5. Close or roll back

Close the release only after required verification passes. Record the approver,
operator, timestamps, deployed commit, check results, migration state, queue/process
state, incidents, and links to redacted evidence.

If a rollback trigger is met, stop further promotion and follow the approved recovery
plan. Prefer redeploying the previous known-good artifact. Do not assume
`php artisan migrate:rollback` is safe: application rollback may be incompatible
with new data or irreversible schema changes. Restore or repair data only through
the reviewed database recovery procedure and its authorized owner.

## Migration and data guardrails

- Never use seeders to update production data and never run production seeders as
  part of a routine release.
- Review each migration for locks, table size, runtime, compatibility with both old
  and new application versions, and rollback/data-loss behavior.
- Use `php artisan migrate --force` only after the exact production migration action
  is explicitly approved. When migration isolation is configured, verify the final
  migration state because a concurrent isolated invocation may do no work.
- Use an idempotent data migration or narrowly scoped maintenance command for live
  records. Identify records by primary key or confirmed stable unique identifier and
  state exact before/after values before approval.
- Never infer that a successful deploy command proves a migration, queue reload,
  cache refresh, or smoke check succeeded; verify each result independently.

## Cache and queue notes

Laravel configuration caching changes how environment values are read. Follow the
repository deployment checklist for `php artisan optimize:clear` and
`php artisan config:cache`, then verify the effective application behavior. Do not
clear shared data caches indiscriminately without understanding load and namespace
impact.

Long-running queue workers retain old application state. Restart or reload them with
the confirmed process manager, allow in-flight jobs to finish according to policy,
and verify worker count, health, and failed-job behavior after deployment.

## Release evidence template

```text
Environment and release:
Commit or artifact:
Previous known-good release:
Operator and approver:
Start/end time and observation window:
Required QA and staging evidence:
Backup/recovery confirmation:
Configuration names changed (no values):
Migration result and verified state:
Application, queue, scheduler, and cache state:
Health and smoke-check results:
Alerts, incidents, and rollback decision:
Redacted evidence links:
Unresolved follow-up:
```
