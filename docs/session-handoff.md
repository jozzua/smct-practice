# Session Handoff

Updated: 2026-07-24 (Asia/Tokyo)

## Repository state

- Working directory:
  `/Users/elyapao/Project/Personal/smct-rehearsal/smct-practice`
- Branch: `main`
- Upstream: `origin/main`
- Latest commit: `87ec003c0305490f19a8bbd29957372c72ddd65d`
  (`Add a plain-text kanban board so work in this repo is visible between sessions`)
- `git pull` was run at the start of this session and reported `Already up to date`.
- The working tree contains uncommitted documentation changes. Do not discard them.

## Work completed in this session

Documentation was consolidated under `docs/`:

- Updated the root `README.md` to replace its embedded development status with links
  to the repository documentation.
- Created `docs/development-log.md` with current status, recent changes, and follow-up
  work.
- Moved the root `kanban.md` to `docs/kanban.md` without changing its task state.
- Kept the existing admin login requirements at `docs/admin-login-prd.md`.

Current uncommitted paths:

```text
 M README.md
 D kanban.md
?? docs/development-log.md
?? docs/kanban.md
?? docs/session-handoff.md
```

The deletion and addition of `kanban.md` represent an intentional move into `docs/`.
Git may display it as a rename after staging.

## Validation already completed

- `git diff --check` passed before this handoff file was added.
- Repository references to the development log, Kanban board, and admin login PRD
  were checked with `rg`.
- Application tests and the frontend build were not run because the changes are
  Markdown-only.

## Current Kanban

### Todo

1. Fix the `DatabaseSeeder::seedOrders()` argument mismatch so
   `php artisan migrate --seed` works on a fresh database.
2. Decide whether the reusable `<x-card>` image prop should replace the inline
   `<img>` on the products page.
3. Add a regression test that protects the Orders list from N+1 queries.

### Doing

- Nothing is currently in progress.

## Planned but unimplemented

Admin login remains a requirements document only. Its next implementation steps are
the `is_admin` user flag, admin middleware, protected `/admin` routes, a dashboard,
conditional navigation, and feature tests. See `docs/admin-login-prd.md`.

## Important project rules

- Use `config('app.name')` for customer-visible company-name text; the current name
  is `SMCT`.
- Do not change `SESSION_COOKIE`, `CACHE_PREFIX`, or `REDIS_PREFIX` as a branding side
  effect.
- Do not use seeders as a production data-migration mechanism.
- Do not edit or commit generated runtime/dependency files.
- Never access or change production infrastructure without explicit authorization.

## Suggested resume steps

```bash
git status --short --branch
git diff --check
git diff -- README.md docs/ kanban.md
```

Then review `docs/development-log.md` and `docs/kanban.md`. If the documentation
reorganization is approved, stage and commit it before selecting a Kanban task.
