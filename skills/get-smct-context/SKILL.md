---
name: get-smct-context
description: Build an evidence-backed, SMCT-only repository briefing from current Git state, project documentation, active work, recent changes, and relevant development logs. Use when an agent is asked to "get SMCT context," explain what changed recently, summarize current project status, prepare a handoff, identify documented next work, or refresh SMCT context documentation.
---

# Get SMCT Context

Bring an agent up to date on the Maligaya Trading Company rehearsal repository without importing unrelated personal or external-project context.

## Scope

- Stay inside the current SMCT repository and its Git metadata.
- Do not invoke personal-memory or global-context skills, search outside the repository, or include non-SMCT projects, work, study, or personal information.
- Keep the default workflow read-only. Modify documentation only when the user explicitly asks to refresh or record context.
- Summarize logs without exposing secrets, session contents, credentials, cookies, tokens, or personal data.
- Never access production infrastructure through this skill.

## Build the Context

1. Read `AGENTS.md` completely and follow its current repository rules.
2. Establish the actual repository state before trusting narrative documents:

   ```bash
   git status --short --branch
   git log -12 --date=iso-local --format='%h %ad %s'
   git diff --stat
   git diff --check
   ```

   Inspect local and remote refs when merge or publication status matters. Do not call work merged merely because it exists on another branch or in an uncommitted diff.
3. Read these sources in order:

   - `docs/kanban.md` for active work and documented next items;
   - `docs/development-log.md` for meaningful recent history;
   - the current exercise and restore boundaries in `docs/rehearsal-learning-record.md`;
   - recent commit messages and changed paths for facts not yet recorded in the docs;
   - relevant PRDs under `docs/` only when their tasks are active or requested.

4. Treat `docs/session-handoff.md` as historical evidence. Compare its update marker, branch, commit, and worktree claims with current Git state; label it stale when they disagree.
5. When the request concerns recent behavior or runtime health, check `storage/logs/laravel.log` when present, repo-local development logs, a `dev/` directory, and TODO notes. Report when a source does not exist. Read only the smallest relevant log window and sanitize the summary.
6. Reconcile conflicts using this priority:

   1. Current Git and filesystem state for what exists now.
   2. `docs/kanban.md` for planned and active work.
   3. The development log and learning record for intent, verification, and restore boundaries.
   4. Older handoff notes for historical context only.

7. Return a concise SMCT-only briefing with:

   - current branch, upstream relationship, and worktree state;
   - recent completed changes, distinguishing committed, merged, branch-only, and uncommitted work;
   - active work and the next documented Kanban items;
   - verification status, relevant runtime findings, and any stale or conflicting documentation;
   - the repository sources checked.

Use exact SKUs, commit identifiers, pull-request numbers, test results, and timestamps when available. Do not guess missing facts.

## Refresh Context Documentation

Only enter this mode when the user asks to update or record the context.

1. Inspect the worktree and preserve unrelated rehearsal edits.
2. Update `docs/development-log.md` with meaningful new SMCT history. Maintain its `Last updated: YYYY-MM-DD HH:MM (Asia/Tokyo)` marker and timestamped headings.
3. Keep `docs/kanban.md` aligned with the actual task state. Move work to Done only after required verification passes.
4. Update `docs/rehearsal-learning-record.md` with the baseline, intent, affected files, verification evidence, and restore boundary for educational changes.
5. Update `docs/session-handoff.md` only when preparing a new handoff; replace stale state with verified current facts.
6. Run `git diff --check`, review the complete documentation diff, and report whether application checks were needed.

Do not rewrite historical claims merely to make the documents look current. Add a correction or a new timestamped entry when facts changed.
