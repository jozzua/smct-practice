# Project Instructions

## Branding and company name

- The current customer-facing company name is `Maligaya Trading Company`.
- Use `config('app.name')` for customer-visible company-name text in Blade views. Do not hard-code the company name in individual pages.
- Before changing branding, search the repository for the current and proposed names. Review configuration, Blade views, email templates, metadata, documentation, and tests.
- `APP_NAME` is the source for Laravel's application name. Updating `.env.example` or a default in `config/app.php` does **not** update an existing local or production `.env` value; an actual environment value overrides both.

## Operational impact of APP_NAME

- In this application, the default session-cookie name, cache prefix, and Redis prefix are derived from `APP_NAME`. A display-name change can therefore invalidate sessions or move cache namespaces.
- Treat `SESSION_COOKIE`, `CACHE_PREFIX`, and `REDIS_PREFIX` as operational identifiers. Do not change them as a side effect of a branding update. If a change is necessary, explain the user impact and obtain approval first.

## Production data and seeders

- Seeders initialize a fresh database. They do not modify an already-populated production database unless someone explicitly runs them, and they must not be used as a production data-migration mechanism.
- Do not assume a display name identifies a record. Before changing live data, inspect the current record and use its primary key or a confirmed stable unique identifier.
- To change an existing production record, prepare an idempotent data migration or a narrowly scoped maintenance command. State the exact records and intended before/after values, then obtain explicit approval before running it.
- When a requirement names a record but the requested final value, identifier, or user impact is ambiguous, stop and ask a concise clarifying question before making a production data change.
- Tests for a data change must assert the intended existing record or stable unique identifier. A test that only proves a fresh seed can create a similar record is insufficient.

## Local runtime files, dev notes, and generated dependencies

- Treat `storage/logs/*`, `storage/framework/sessions/*`, `node_modules/`, `vendor/`, built assets, and other generated runtime or dependency files as local artifacts unless a task explicitly asks otherwise.
- Do not edit or commit generated log files, session files, dependency directories, or built assets as part of application changes.
- When investigating recent behavior, check the Laravel log at `storage/logs/laravel.log` when present, relevant local dev logs, any repo-local `dev/` directory, and current TODO notes before changing code.
- If the repo has no `dev/` directory, dev log, or TODO notes, mention that in the investigation summary instead of inventing one.
- When inspecting logs or sessions, summarize only relevant findings. Do not expose secrets, session payloads, cookies, tokens, passwords, personal data, or other sensitive values.
- If a runtime issue requires reproduction, prefer Laravel logs, tests, and targeted Artisan commands over manually changing session files.

## Educational rehearsal and restore points

- Treat rehearsal changes as educational exercises rather than production work.
- Keep `docs/rehearsal-learning-record.md` current with the baseline commit, exercise
  intent, affected files, and verification results.
- Distinguish committed exercises, tracked working-tree changes, and ignored local
  environment changes so they can be restored independently.
- Do not reset, revert, discard, or force-push rehearsal work without explicit user
  approval. Verify the exact restore target and preserve the learning record first.

## Kanban board and work tracking

- Treat `docs/kanban.md` as the source of truth for active repository work.
- When updating `docs/development-log.md`, include both the date and time in
  Asia/Tokyo. Keep a `Last updated: YYYY-MM-DD HH:MM (Asia/Tokyo)` marker and use
  `YYYY-MM-DD HH:MM (Asia/Tokyo)` for new history headings.
- Before starting an implementation task, read the board and inspect the working
  tree so existing work and unrelated rehearsal changes remain visible.
- When picking up a Todo item, move it to Doing and add your name. Keep one task per
  line and place the newest item at the top of its section.
- When the task is complete and verified, move it to Done and remove your name.
- Keep the board update in the same pull request or change set as the work it
  describes. Do not mark a task Done when required verification is failing or was
  not run.

## Repository skills

- Repo-scoped Codex skills live under `skills/`. Read the matching `SKILL.md`
  completely before using a skill and follow its workflow and guardrails.
- Use `$get-smct-context`
  (`skills/get-smct-context/SKILL.md`) for SMCT repository briefings, recent-change
  summaries, handoffs, or explicit context-documentation refreshes. Keep its output
  limited to SMCT and read-only unless the user asks to update the documentation.
- Use `$swap-product-card-photo`
  (`skills/swap-product-card-photo/SKILL.md`) for catalog product-photo changes,
  product-image merge conflicts, or regressions involving the shared product card.
- Use `$apply-product-sale-pricing`
  (`skills/apply-product-sale-pricing/SKILL.md`) for sale-price changes or pricing
  inconsistencies across the catalog, checkout, VAT, shipping, and stored order
  items.
- Use `$build-laravel-qa-pipeline`
  (`skills/build-laravel-qa-pipeline/SKILL.md`) to design, implement, or audit local
  QA commands, GitHub Actions, pull-request gates, browser smoke tests, staging
  verification, dependency security, and policy-gated AI review.
- Use `$operate-laravel-releases`
  (`skills/operate-laravel-releases/SKILL.md`) when preparing, executing, auditing,
  or documenting a release, including preflight, production approval, migrations,
  runtime reloads, health and smoke checks, evidence, and rollback planning.
- When adding, renaming, or removing a repo-scoped skill, update this list and the
  relevant learning record or development documentation in the same change.

## Standard local checks

- Before committing PHP or backend changes, run `composer test` when practical.
- Before committing frontend asset, Blade, CSS, or JavaScript changes, run `npm run build` when practical.
- For a fresh checkout setup, use `composer setup`.
- If a check cannot run because of environment limitations, report the exact command, failure reason, and whether the limitation is local-only.

## Dependency management

- Use Composer for PHP dependencies and npm for frontend dependencies.
- Do not manually edit files inside `vendor/` or `node_modules/`.
- Commit lockfile changes only when dependency versions intentionally change.

## Deployment checklist

When a branding or environment-backed configuration change is approved for deployment:

1. Update the source-controlled defaults and every customer-visible usage.
2. Update the real deployment environment or `.env`; do not rely on `.env.example`.
3. On the target host, run `php artisan optimize:clear` followed by `php artisan config:cache`.
4. Verify the live page directly. Confirm the document title, header brand, page content, footer, and relevant response headers show the intended value.
5. For data changes, query the live record after deployment and verify its identifier and final values.
6. Do not claim the change is complete based only on Git history or a local environment.

Never access or change production infrastructure without explicit user authorization.
