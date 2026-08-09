---
name: build-laravel-qa-pipeline
description: Design, implement, audit, or extend a staged QA pipeline for this Laravel repository, including local QA commands, GitHub Actions, pull-request gates, isolated migration and seeder smoke tests, browser journeys, dependency security, staging verification, and policy-gated AI review. Use when asked to set up CI or QA, add PR checks, protect main, improve test automation, add deployment gates, or prepare the SMCT S3 QA workflow.
---

# Build Laravel QA Pipeline

Build quality in layers: deterministic checks first, accountable human review
second, staging verification third, and AI review only after an explicit data-policy
decision.

## 1. Inspect before changing

Read `AGENTS.md`, `docs/development-log.md`, `docs/kanban.md`, the current working
tree, `composer.json`, `package.json`, `phpunit.xml`, `tests/`, deployment notes, and
any existing `.github/workflows/`.

Confirm:

- the active PHP, Laravel, Node, and Vite constraints from repository files;
- the current test and build commands;
- the production database and deployment flow;
- who can administer GitHub rules and secrets;
- whether private diffs may be sent to an AI provider.

Preserve unrelated working-tree changes. For implementation work, put the QA task
in Doing before editing.

## 2. Establish the baseline

Run the existing focused checks, `composer test`, and `npm run build` before
changing the pipeline. Record exact results and distinguish existing failures from
new regressions.

Inventory missing layers rather than assuming them. Check for:

- formatting or static-analysis checks;
- feature and unit tests;
- frontend production builds;
- migration and full-seeder smoke coverage;
- real-browser critical journeys;
- dependency review and update automation;
- branch rules, staging checks, and rollback documentation.

Do not invent coverage targets or supported runtime versions. Measure or verify
them first.

## 3. Define the QA contract

Use stable check names because GitHub branch rules depend on them:

| Check | Required behavior |
|---|---|
| `php-style` | Run Pint in check-only mode. |
| `php-tests` | Run the complete PHPUnit/Laravel suite. |
| `frontend-build` | Install locked npm dependencies and build Vite assets. |
| `database-smoke` | Run fresh migrations and the complete seeder on an isolated database. |
| `dependency-review` | Reject newly introduced vulnerable dependencies when the repository plan supports it. |
| `browser-smoke` | Exercise critical customer journeys after this check is stable. |

Add static analysis and coverage as measured improvements, not arbitrary first-day
blockers. Start with a recorded coverage baseline, then prevent material regression.

## 4. Provide one local entry point

Add or update a Composer `qa` script that reuses the same deterministic commands as
CI:

1. clear test configuration;
2. run Pint with `--test`;
3. run the Laravel test suite;
4. run the frontend production build.

Keep dependency installation in setup or CI rather than reinstalling packages on
every local QA run. Do not edit or commit `vendor/`, `node_modules/`, built assets,
logs, sessions, or other generated files.

## 5. Implement GitHub Actions safely

Create `.github/workflows/qa.yml` for pull requests and pushes to `main`. Add
`merge_group` only when the repository uses a merge queue.

Apply these defaults:

- use minimal permissions, normally `contents: read`;
- cancel superseded runs on the same branch;
- install from `composer.lock` and `package-lock.json`;
- key dependency caches from lockfiles;
- pin a PHP version allowed by `composer.json`;
- pin a Node LTS version supported by the repository's Vite version;
- keep required job names stable;
- never expose deployment or API secrets to untrusted pull-request code;
- avoid `pull_request_target` unless its security implications are explicitly
  understood and required.

Run `database-smoke` against a temporary SQLite file or an isolated CI service
database. Never point CI migration or seeding commands at a populated local,
staging, or production database.

## 6. Add browser coverage in a second stage

Automate a small number of high-value journeys:

1. browse the catalog and verify product images and prices;
2. add a quantity, complete checkout, and see confirmation;
3. sign up or log in and inspect the relevant order flow.

Use Playwright, Laravel Dusk, or the existing project-standard browser harness.
Upload screenshots and traces only on failure. Keep browser checks advisory until
they have proved repeatable; do not make flaky automation a required merge gate.

## 7. Protect `main`

After the workflow succeeds on several representative pull requests, configure a
GitHub ruleset or branch protection rule to:

- require pull requests;
- require the deterministic checks;
- require at least one approval from someone other than the latest pusher;
- dismiss stale approvals after code changes;
- require conversation resolution;
- block force-pushes and deletion;
- require Code Owner review for sensitive paths when ownership is clear.

Do not apply repository-admin settings without authorization. Report the exact
settings an administrator must enable when direct configuration is unavailable.

## 8. Gate deployment

After merge to `main`, deploy to staging and verify migrations, application health,
and the critical browser smoke journey. Require an explicit production approval and
retain an exact rollback target.

Never access production infrastructure, change live data, or run seeders against
production without explicit authorization. Treat a successful local or CI run as
necessary evidence, not proof of a successful deployment.

## 9. Gate AI review on policy

Before sending private source, diffs, logs, or test output to an AI service, obtain
an explicit written data-policy decision from the repository owner.

If approval is absent:

- keep the deterministic pipeline fully operational;
- demonstrate AI review on the rehearsal or redacted repository;
- leave a documented activation step for later.

If approval exists, add AI review after deterministic CI. Start it as advisory and
have it report change summary, risk areas, missing tests, security concerns, and
documentation drift. Keep a human reviewer accountable for merge decisions. Never
send secrets, session data, credentials, customer data, or unredacted sensitive
logs.

## 10. Verify and hand off

Run all affected focused checks, `composer test`, `npm run build`, the isolated
migration/seeder smoke, and `git diff --check`. Inspect the workflow diff for
permissions, secret exposure, and unstable job names.

Do not mark the Kanban task Done while a required check is failing or unrun. In the
handoff, report:

- files changed;
- commands and results;
- required GitHub check names;
- branch or ruleset settings still needing an administrator;
- deployment and rollback requirements;
- the AI data-policy decision and any deferred AI step;
- known gaps such as browser flakiness or missing production verification.

## Guardrails

- Keep deterministic QA useful without AI.
- Prefer a few reliable required checks over many noisy checks.
- Do not silently add dependencies, secrets, repository permissions, or production
  access.
- Do not make dependency advisories or coverage thresholds blocking until their
  policy and baseline are explicit.
- Preserve rehearsal restore points and keep
  `docs/rehearsal-learning-record.md` current.
