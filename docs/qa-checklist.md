# QA Checklist

QA in this repo happens at three altitudes. A change is not "done" until all three
pass — a green test at the wrong altitude proves nothing (the catalog photos were
green at levels 1 and 2 and still invisible on the live site until the SKU matched
the live record).

## The three altitudes

1. **Focused test** — does *this change* work?
   `php artisan test --filter=YourFeatureTest`
2. **Full suite** — did it break *anything else*?
   `composer test` — report unrelated failures separately; never claim the suite
   passed when it didn't.
3. **Live verification** — does it work *where it actually runs*?
   Load the live page after merge + deploy. Git history is not deployment;
   deployment is not behavior (environment values override repo defaults, and live
   data is not local data).

## Writing tests: the spec is the test plan

Every board ticket links a PRD whose **Definition of Done is the assertion list**.
Write the test first, from the DoD, and watch it fail **for the right reason**
(feature missing — not a typo) before implementing.

When an agent drafts the test or the fix, two standing rules:

- **Tell it: don't modify the tests.** A stuck agent will fix the test instead of
  the code.
- **An AI edit you can't verify is a liability.** No test, no merge.

Patterns to copy from the existing suite:

| Existing test | Pattern |
|---|---|
| `ProductCardPhotoTest` | Contract testing — assert the convention (one URL template, `<x-card>`, no inline `<img>`), including a SKU **with a space** to prove encoding |
| `OrdersIndexTest` (N+1 guard) | Regression pinning — query count stays constant from 1 row to 10 |
| `ProductQuantityPickerTest` | Markup/accessibility QA — assert `role`/`aria` present and the old element **absent** |
| `DatabaseSeederTest` | Data QA — a fresh seed must succeed and produce the expected record |

## Reviewer checklist (every PR)

- [ ] The diff matches the ticket's PRD — nothing extra, smallest change that
      satisfies the DoD.
- [ ] Work arrived on a **branch of this repo** (not a fork), and the board line
      moved in the same PR.
- [ ] Focused test exists, passes, and asserts the DoD (not just "it renders").
- [ ] Full suite result stated honestly — unrelated failures reported, not absorbed.
- [ ] Identifiers verified against **live** records where the change touches data
      or per-record assets (a display name is not an identifier).
- [ ] Tests were not modified to make the change pass (diff the test files first).

## After the merge

- [ ] Deploy picked it up (the cron ships `main` within ~1–2 minutes).
- [ ] The change is visible/behaving **on the live page** — verify the page,
      then move the board line to Done.
