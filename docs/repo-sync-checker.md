# Repo Sync Checker Skill

Use `$repo-sync-checker` when you need Codex to act as a system checker before,
during, or after git synchronization work in this Laravel repository. It is meant
for `fetch`, `pull`, merge, rebase review, merge-conflict triage, and post-merge
verification where local rehearsal changes must be protected.

## When to invoke it

- Before pulling from `origin/main` or integrating another branch.
- When the worktree is dirty, detached, ahead, behind, or already conflicted.
- When local rehearsal work, ignored environment files, or generated runtime files
  could be confused with intentional source changes.
- After resolving conflicts, before marking work complete or handing it back.

## What it checks

1. Reads `AGENTS.md`, `docs/kanban.md`, development notes, and relevant rehearsal
   records before changing repository state.
2. Inspects the current branch, upstream tracking, staged changes, unstaged
   changes, untracked files, and likely generated artifacts.
3. Fetches and reviews incoming commits before recommending a sync strategy.
4. Separates upstream changes, local source edits, rehearsal work, and ignored
   environment/runtime files.
5. Reviews conflicts file by file, especially docs, tests, migrations, seeders,
   lockfiles, shared Blade views, and repo-scoped skill references.
6. Runs focused verification after integration and reports any checks that could
   not run.

## Approval boundaries

The skill must ask for explicit user approval before destructive or history-shaping
operations, including:

- discarding or overwriting local changes;
- `git reset --hard`;
- destructive `git clean`;
- rebasing shared or published work;
- force-pushing;
- deleting branches or tags;
- resolving conflicts by blindly taking all local or all incoming changes;
- changing production `.env`, deployment configuration, live data, or
  infrastructure.

It should also avoid editing or committing generated files such as logs, sessions,
`vendor/`, `node_modules/`, and built assets unless the user explicitly asks.

## Expected verification

After a sync or conflict resolution, the skill should run the smallest practical
checks for the files touched:

- `composer test` for PHP or backend behavior changes when practical;
- `npm run build` for Blade, CSS, JavaScript, or frontend asset changes when
  practical;
- `git diff --check` for whitespace and conflict-marker mistakes;
- dependency or security checks when `composer.lock` or `package-lock.json`
  changed intentionally.

The final handoff should state the starting repository state, integrated upstream
changes, conflicts and resolutions, protected local or rehearsal changes, checks
run, checks skipped, and any remaining approval-needed actions.
