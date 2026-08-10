---
name: repo-sync-checker
description: Safely synchronize this Laravel repository with upstream work, review pull/merge/rebase risk, protect local and rehearsal changes, resolve merge conflicts deliberately, and run post-merge QA. Use when acting as a system checker for git pull, fetch, merge, rebase, conflict review, branch sync, or post-integration verification in the SMCT repository.
---

# Repo Sync Checker

Synchronize the repository only after you understand the current worktree, the
incoming change, and the restore boundary. Preserve local and rehearsal work unless
the user explicitly approves a different outcome.

## 1. Inspect before syncing

Read `AGENTS.md`, `docs/kanban.md`, `docs/development-log.md`, and
`docs/rehearsal-learning-record.md`. Check the current worktree before changing
anything:

- run `git status --short --branch`;
- identify detached HEAD, ahead/behind, staged, unstaged, untracked, ignored, and
  conflicted files;
- inspect current branch, upstream, latest local commit, and intended remote target;
- check `storage/logs/laravel.log` when present and summarize only relevant
  non-sensitive findings;
- note when there is no repo-local `dev/` directory, dev log, TODO notes, or
  Laravel log.

Treat `docs/kanban.md` as the source of truth for active work. Do not move Kanban
items unless the sync task itself requires it and the change belongs in the same
change set.

## 2. Protect local and rehearsal changes

Separate committed exercises, tracked working-tree changes, and ignored local
environment changes. Compare rehearsal-sensitive work with
`docs/rehearsal-learning-record.md` before deciding what can move.

Do not reset, revert, discard, clean, overwrite, force-push, or checkout over local
work without explicit user approval. If local work blocks a sync, propose the
lowest-risk preservation path: commit, branch, stash, patch export, or stop for user
decision.

Do not edit or commit generated runtime or dependency files, including
`storage/logs/*`, `storage/framework/sessions/*`, `vendor/`, `node_modules/`, built
assets, or local environment files.

## 3. Review upstream before integrating

Fetch before integrating. Prefer `git fetch --prune`, then inspect incoming commits
and diffs before pulling or merging.

Summarize incoming changes that affect:

- files already changed locally;
- `AGENTS.md`, `README.md`, docs, skills, and Kanban records;
- Laravel models, controllers, Blade views, routes, tests, migrations, and seeders;
- `composer.json`, `composer.lock`, `package.json`, and package lockfiles;
- configuration, environment-backed behavior, sessions, cache, Redis, or deployment
  notes.

Do not update dependencies, change branding, alter operational identifiers, run
seeders against populated data, or touch production infrastructure as a side effect
of synchronization.

## 4. Choose the safest integration strategy

Use the strategy that preserves intent and history:

- fast-forward when possible and appropriate;
- merge when preserving local rehearsal commits or visible conflict review matters;
- rebase only when explicitly requested or clearly safe for unpublished local work;
- stop before destructive repair if the repository is detached, dirty, conflicted,
  or pointed at an unexpected upstream.

Explain the expected result before operations that change branch history or combine
substantial work.

## 5. Resolve conflicts deliberately

List conflicted files and inspect each conflict in context. For every conflict,
identify the local intent, incoming intent, and combined behavior that should remain.

Pay special attention to conflicts in `docs/kanban.md`,
`docs/development-log.md`, `docs/rehearsal-learning-record.md`, repo-scoped skills,
Blade components, seeders, migrations, product image assets, tests, and lockfiles.

Do not resolve by blindly choosing ours or theirs. Use wholesale ours/theirs only
after explicit approval and after stating what will be lost.

## 6. Verify after integration

Run checks scaled to the merged surface:

1. run focused tests for touched Laravel behavior;
2. run Pint for changed PHP when practical;
3. run `composer test` for backend changes when practical;
4. run `npm run build` for Blade, CSS, JavaScript, or asset-pipeline changes when
   practical;
5. run `git diff --check`;
6. inspect dependency lockfile changes and run `composer audit`, npm audit, or the
   repository's dependency-security check when practical and relevant.

Report exact commands, pass/fail status, and local-only limitations. Do not mark
work Done while required verification is failing or unrun unless the limitation is
explicitly recorded.

## 7. Handoff clearly

End with a concise sync report:

- starting branch and sync state;
- upstream commits or merge base reviewed;
- strategy used and why;
- conflicts found and how each was resolved;
- local and rehearsal work preserved;
- files changed;
- verification run and results;
- checks skipped and why;
- remaining risks, approvals, or follow-up actions.

## Approval Boundaries

Obtain explicit approval before:

- `git reset --hard`, destructive `git clean`, or discarding changes;
- force-pushing, deleting branches or tags, or rebasing shared work;
- resolving conflicts by wholesale ours/theirs selection;
- changing `.env`, deployment settings, live data, or production infrastructure;
- running migrations or seeders against non-local populated databases;
- changing `SESSION_COOKIE`, `CACHE_PREFIX`, `REDIS_PREFIX`, or other operational
  identifiers;
- sending private diffs, logs, secrets, customer data, or session data to an
  external AI or security service.
