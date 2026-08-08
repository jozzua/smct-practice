// Builds the AI QA report for a pull request and posts it as a single,
// self-updating PR comment.
//
// Two layers, deliberately separated:
//
//   1. MECHANICAL  - computed from the diff. Always runs. Deterministic,
//                    free, and impossible to hallucinate. These are the
//                    checks a reviewer most often skips because they are
//                    tedious (did the test file change? did the board move?).
//   2. NARRATIVE   - an LLM summary. Only runs when OPENAI_API_KEY is set.
//                    Advisory. If the key is absent the report still posts
//                    with layer 1 and says what is missing.
//
// The section headings track the six reviewer-checklist items in
// docs/qa-checklist.md. Keep them in sync: if the checklist changes, this
// report should change with it.

import { execFileSync } from 'node:child_process';

const {
  GITHUB_TOKEN,
  OPENAI_API_KEY,
  OPENAI_MODEL,
  PR_NUMBER,
  BASE_SHA,
  HEAD_SHA,
  REPO,
} = process.env;

const MARKER = '<!-- ai-qa-report -->';

function git(...args) {
  return execFileSync('git', args, { encoding: 'utf8', maxBuffer: 32 * 1024 * 1024 });
}

// The merge base, not the base branch tip: we want what this PR actually
// changed, not everything that landed on main since it was opened.
function mergeBase() {
  try {
    return git('merge-base', BASE_SHA, HEAD_SHA).trim();
  } catch {
    return BASE_SHA;
  }
}

const base = mergeBase();

const changedFiles = git('diff', '--name-status', base, HEAD_SHA)
  .split('\n')
  .filter(Boolean)
  .map((line) => {
    const [status, ...rest] = line.split('\t');
    return { status, path: rest[rest.length - 1] };
  });

const diff = git('diff', '--unified=3', base, HEAD_SHA);

// ---------------------------------------------------------------- classify

const isTest = (p) => p.startsWith('tests/') || p.endsWith('Test.php');
const isSource = (p) => (p.startsWith('app/') || p.startsWith('routes/') || p.startsWith('resources/')) && !isTest(p);
const isData = (p) => p.startsWith('database/migrations/') || p.startsWith('database/seeders/') || p.startsWith('database/factories/');
const isDocs = (p) => p.startsWith('docs/') || p === 'AGENTS.md' || p === 'README.md';

const testFiles = changedFiles.filter((f) => isTest(f.path));
const sourceFiles = changedFiles.filter((f) => isSource(f.path));
const dataFiles = changedFiles.filter((f) => isData(f.path));
const docFiles = changedFiles.filter((f) => isDocs(f.path));
const boardMoved = changedFiles.some((f) => f.path === 'docs/kanban.md');

// Removed assertion lines inside test files are the signal for
// "the agent fixed the test instead of the code". Walk the diff per file so a
// deletion in app/ is never mistaken for a deletion in tests/.
function removedAssertions() {
  const hits = [];
  let current = null;
  for (const line of diff.split('\n')) {
    const header = line.match(/^\+\+\+ b\/(.+)$/);
    if (header) {
      current = header[1];
      continue;
    }
    if (!current || !isTest(current)) continue;
    if (line.startsWith('-') && !line.startsWith('---') && /assert|expect|->see|shouldReceive/i.test(line)) {
      hits.push({ file: current, line: line.slice(1).trim() });
    }
  }
  return hits;
}

const droppedAssertions = removedAssertions();
const newTestFiles = testFiles.filter((f) => f.status === 'A');

// ------------------------------------------------------------ check builder

const PASS = '✅';
const WARN = '⚠️';
const INFO = 'ℹ️';

const checks = [];
const add = (icon, item, verdict) => checks.push({ icon, item, verdict });

// 1. Diff matches the ticket - smallest change that satisfies the DoD.
add(
  INFO,
  'Diff matches the PRD',
  `${changedFiles.length} file(s): ${sourceFiles.length} source, ${testFiles.length} test, ${dataFiles.length} data, ${docFiles.length} docs. **A human still has to confirm this matches the ticket** — the report can only count files, not read intent.`,
);

// 2. Branch of this repo + board line moved in the same PR.
add(
  boardMoved ? PASS : WARN,
  'Board moved in the same PR',
  boardMoved
    ? '`docs/kanban.md` changed in this PR.'
    : '`docs/kanban.md` was **not** touched. AGENTS.md requires the board update to ship with the work it describes.',
);

// 3. Focused test exists.
add(
  testFiles.length > 0 ? PASS : WARN,
  'Focused test present',
  testFiles.length > 0
    ? `${testFiles.length} test file(s) changed${newTestFiles.length ? `, ${newTestFiles.length} newly added` : ''}: ${testFiles.map((f) => `\`${f.path}\``).join(', ')}`
    : sourceFiles.length > 0
      ? `**${sourceFiles.length} source file(s) changed with no test change.** No test, no merge.`
      : 'No source change, so no test expected.',
);

// 4. Full suite - reported by qa.yml, not re-run here.
add(
  INFO,
  'Full suite result',
  'See the `php-tests` check on this PR. Report unrelated failures separately — never absorb them.',
);

// 5. Identifiers verified against live records.
add(
  dataFiles.length > 0 ? WARN : PASS,
  'Live identifiers verified',
  dataFiles.length > 0
    ? `Touches migrations/seeders/factories: ${dataFiles.map((f) => `\`${f.path}\``).join(', ')}. **A display name is not an identifier** — verify against the live record before merge.`
    : 'No migration, seeder, or factory changes.',
);

// 6. Tests not weakened to make the change pass. The one the reviewer
//    checklist calls out explicitly, and the easiest to automate.
add(
  droppedAssertions.length === 0 ? PASS : WARN,
  'Tests not weakened',
  droppedAssertions.length === 0
    ? 'No assertions removed from test files.'
    : `**${droppedAssertions.length} assertion line(s) removed from tests.** Diff the test files before trusting a green run:<br>${droppedAssertions
        .slice(0, 10)
        .map((h) => `• \`${h.file}\` — \`${h.line.slice(0, 100)}\``)
        .join('<br>')}`,
);

// ------------------------------------------------------------- AI narrative

async function narrative() {
  if (!OPENAI_API_KEY) {
    return `> ${INFO} **AI narrative skipped** — no \`OPENAI_API_KEY\` secret is configured on this repository. The mechanical checks above ran normally. Add the secret in **Settings → Secrets and variables → Actions** to enable it.`;
  }

  // Keep the payload bounded. A very large diff costs money and produces a
  // worse summary than the mechanical checks already give.
  const MAX = 60000;
  const truncated = diff.length > MAX;
  const payload = truncated ? `${diff.slice(0, MAX)}\n\n[diff truncated at ${MAX} characters]` : diff;

  const prompt = `You are reviewing a pull request on a small Laravel storefront used for developer training.

Reply in GitHub-flavoured markdown, under 250 words, with exactly these three sections:

**What changed** — two or three sentences, plain language, describing intent rather than restating filenames.
**Risk areas** — a short bullet list of things most likely to break, or "None obvious." Be specific about files and behaviour.
**Missing tests** — behaviour changed by this diff that no test appears to cover, or "Coverage looks proportionate."

Rules: be concise and concrete. Do not invent files or behaviour that is not in the diff. Do not repeat the mechanical checklist. If the diff is trivial, say so briefly rather than padding.

Diff:
${payload}`;

  try {
    const res = await fetch('https://api.openai.com/v1/chat/completions', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${OPENAI_API_KEY}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        model: OPENAI_MODEL || 'gpt-4o-mini',
        messages: [{ role: 'user', content: prompt }],
      }),
    });

    if (!res.ok) {
      const detail = await res.text();
      return `> ${WARN} **AI narrative unavailable** — the model API returned ${res.status}. The mechanical checks above are unaffected.\n>\n> \`${detail.slice(0, 300).replace(/\n/g, ' ')}\``;
    }

    const json = await res.json();
    const text = json.choices?.[0]?.message?.content?.trim();
    return text || `> ${WARN} **AI narrative unavailable** — the model returned an empty response.`;
  } catch (err) {
    return `> ${WARN} **AI narrative unavailable** — ${String(err.message).slice(0, 200)}. The mechanical checks above are unaffected.`;
  }
}

// ------------------------------------------------------------------ compose

const warnings = checks.filter((c) => c.icon === WARN).length;

const body = `${MARKER}
## AI QA report

${warnings === 0 ? `${PASS} **No mechanical warnings.**` : `${WARN} **${warnings} item(s) need a human look.**`} Advisory only — merge is gated by the \`QA\` workflow, not by this comment.

### Reviewer checklist
_Mirrors \`docs/qa-checklist.md\`. Mechanical checks only — they cannot judge intent._

| | Check | Finding |
|---|---|---|
${checks.map((c) => `| ${c.icon} | ${c.item} | ${c.verdict.replace(/\n/g, '<br>')} |`).join('\n')}

### Narrative

${await narrative()}

### Altitude 3 is still yours
A green pipeline is altitudes 1 and 2. After merge, the deploy cron ships \`main\` within ~1–2 minutes — **load the live page and confirm the behaviour** before moving the board line to Done. Git history is not deployment; deployment is not behaviour.

<sub>Generated for \`${HEAD_SHA?.slice(0, 7)}\` · ${changedFiles.length} file(s) changed</sub>`;

// ------------------------------------------------------------------- upsert

// DRY_RUN prints the report instead of posting it, so the pipeline can be
// exercised locally without a token:
//   BASE_SHA=main HEAD_SHA=HEAD DRY_RUN=1 node .github/scripts/qa-report.mjs
if (process.env.DRY_RUN) {
  console.log(body);
  process.exit(0);
}

const api = async (path, init = {}) =>
  fetch(`https://api.github.com/repos/${REPO}${path}`, {
    ...init,
    headers: {
      Authorization: `Bearer ${GITHUB_TOKEN}`,
      Accept: 'application/vnd.github+json',
      'Content-Type': 'application/json',
      ...(init.headers || {}),
    },
  });

// One comment per PR, edited in place — a fresh comment on every push turns a
// busy PR into an unreadable wall.
const existing = await api(`/issues/${PR_NUMBER}/comments?per_page=100`).then((r) => r.json());
const mine = Array.isArray(existing) ? existing.find((c) => c.body?.includes(MARKER)) : null;

const res = mine
  ? await api(`/issues/comments/${mine.id}`, { method: 'PATCH', body: JSON.stringify({ body }) })
  : await api(`/issues/${PR_NUMBER}/comments`, { method: 'POST', body: JSON.stringify({ body }) });

if (!res.ok) {
  console.error(`Failed to post report: ${res.status} ${await res.text()}`);
  process.exit(1);
}

console.log(`QA report ${mine ? 'updated' : 'posted'} on PR #${PR_NUMBER} (${warnings} warning(s)).`);
