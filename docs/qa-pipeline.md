# QA Pipeline

Two workflows run on every pull request. They do different jobs and the
difference matters:

| Workflow | File | Blocks merge? | Needs a secret? |
|---|---|---|---|
| **QA** | `.github/workflows/qa.yml` | Yes — this is the gate | No |
| **AI QA Report** | `.github/workflows/ai-qa-report.yml` | No — advisory only | Optional |

**Deterministic checks decide. AI advises.** That order is deliberate. An AI
review that can block a merge becomes a thing people learn to bypass; an AI
review that only advises stays useful. And because the gate needs no secrets, it
works identically on fork pull requests, where secrets are unavailable.

## The gate: `QA`

Four jobs, run in parallel. The names are stable because GitHub branch rules
reference them **by name** — renaming a job silently disables the rule that
required it.

| Check | What it proves |
|---|---|
| `php-style` | Pint formatting is clean (`vendor/bin/pint --test`). |
| `php-tests` | The full suite passes. `phpunit.xml` pins SQLite `:memory:`, so it never touches a real database. |
| `frontend-build` | `npm ci` installs from the lockfile and Vite builds. |
| `database-smoke` | A **fresh migrate + full seed** succeeds on a throwaway SQLite file. |

`database-smoke` exists because the test suite runs against `:memory:` and never
executes the real seeders end to end. A broken seeder passes every test and then
breaks the next person to set up the project. That is exactly the failure the
`DatabaseSeederTest` pattern in `docs/qa-checklist.md` is about, raised to CI.

### Running the same checks locally

```bash
composer qa
```

Same four commands, same order as CI. If this passes locally, CI should agree —
if it does not, that gap is itself the bug.

## The advisor: `AI QA Report`

Posts **one** comment per pull request, edited in place on each push. Its
sections map onto the six-item reviewer checklist in `docs/qa-checklist.md` —
the automation reinforces the habit the team already practises rather than
inventing a competing rubric.

The report has two layers:

**1. Mechanical** — computed from the diff. Always runs, costs nothing, and
cannot hallucinate:

- Did `docs/kanban.md` move in the same PR? (`AGENTS.md` requires it.)
- Did any test file change, and were any **assertions removed**?
- Were migrations, seeders, or factories touched? (Then verify live identifiers.)

The assertion check is the important one. The standing rule in the QA checklist
is *"a stuck agent will fix the test instead of the code"* — this looks for
exactly that signature and quotes the removed lines back at you.

**2. Narrative** — an LLM summary: what changed, risk areas, missing tests. This
is the only part that needs an API key, and it **degrades gracefully**: with no
key configured the report still posts with the mechanical checks and a note
saying the narrative was skipped.

### Enabling the narrative

1. **Settings → Secrets and variables → Actions → New repository secret**
2. Name: `OPENAI_API_KEY`
3. Optionally set a repository *variable* `OPENAI_MODEL` to pin a model
   (defaults to `gpt-4o-mini`).

An API key is **not** the same as a ChatGPT Plus seat. Plus covers Codex in the
editor and on the web; the API is separately billed. If you would rather not add
a key, Codex's own GitHub code review is included with your existing seats and
covers similar ground — see below.

### Testing it without opening a PR

```bash
BASE_SHA=main HEAD_SHA=HEAD DRY_RUN=1 node .github/scripts/qa-report.mjs
```

Prints the report to your terminal instead of posting it.

## Fork pull requests

The AI report is skipped entirely on pull requests from forks. This is not a
limitation to work around — GitHub withholds secrets from fork PRs so that
untrusted code cannot read them, and `pull_request_target` (the trigger that
*would* expose them) is a well-known way to leak repository secrets.

It also matches the house rule already in the checklist: work should arrive on a
**branch of this repo**, not a fork. The gate (`QA`) still runs on forks.

## Pointing this at a private repository

Everything above runs on this practice repo, which is public and contains no
real data. Before running the **narrative** layer against private code, get an
explicit data-policy decision — that step sends diffs to a third-party model
provider. The mechanical layer sends nothing anywhere and is safe to enable
immediately.

If the policy answer is "not yet":

- Keep `qa.yml` and the mechanical half of the report. They are the majority of
  the value and need no approval.
- Leave the narrative disabled by simply not setting `OPENAI_API_KEY`. No code
  change required — the workflow already handles the key being absent.

## Protecting `main`

Once the checks have proved themselves on a few real pull requests, require them
in **Settings → Rules → Rulesets**:

- Require a pull request before merging
- Require status checks: `php-style`, `php-tests`, `frontend-build`,
  `database-smoke`
- Dismiss stale approvals when new commits are pushed
- Block force-pushes

Do not require `ai-qa-report`. It is advisory by design, and requiring it would
make a third-party API outage block every merge.

## What this does not do

- It does not judge whether the diff matches the ticket. Nothing automated can;
  that stays a human job and the report says so.
- It does not verify live behaviour. A green pipeline is altitudes 1 and 2 of
  the three in `docs/qa-checklist.md`. **Altitude 3 — load the live page after
  the deploy cron ships `main` — is still yours.**
