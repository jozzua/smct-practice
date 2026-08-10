# Development Log

This log records meaningful repository changes and the current development state.
The [Kanban board](kanban.md) is the source of truth for active work.

## 2026-08-10

- Merged a staged pull-request QA pipeline with four deterministic gates:
  `php-style`, `php-tests`, `frontend-build`, and `database-smoke`.
- Added a separate advisory QA report that computes review signals from the diff,
  optionally adds an AI narrative, and cannot block a merge when secrets or the AI
  provider are unavailable.
- Added `composer qa` as the local entry point for the deterministic style, test,
  and frontend-build checks.
- Pinned Composer dependency resolution to the production PHP 8.3.6 runtime and
  refreshed the lockfile so production installs remain reproducible.
- Removed the unused database-configuration import exposed by the new Pint gate and
  corrected the advisory report so documentation-only changes do not receive a
  contradictory missing-test warning.

## 2026-07-28

- Added the three-altitude QA checklist: focused tests, the complete suite, and live
  post-deployment verification.
- Documented the standing pull-request review rules, including PRD-based tests,
  Kanban movement, exact live identifiers, unchanged test intent, and honest
  reporting of unrelated failures.

## 2026-07-27

- Replaced the catalog photo for Classic Flat Iron SKU `LI-8867` with a CC0
  museum photo while preserving the shared SKU-based image resolver.
- Restored the original customer-facing name, `Maligaya Trading Company`, through
  `APP_NAME` and `config('app.name')` while preserving the existing session, cache,
  and Redis identifiers.
- Established `Product::imageUrl()` as the single SKU-based product-photo resolver,
  with local images for SKUs `MF-5120`, `CQ-8868`, `NL-8805`, `XK-0093`,
  `LI-8867`, `PP-1112`, and `YS-7893` and a deterministic fallback.
- Replaced the generated image for live Classic Airpot SKU `CQ-8868` with Sarah
  Joy's CC BY-SA 2.0 photograph of a vintage Japanese Peacock pump pot.
- Replaced the generated image for live catalog SKU `NL-8805` with Alf van Beem's
  CC0 photograph of an old gas stove at Malmö's Science and Maritime House.
- Fixed the `DatabaseSeeder::seedOrders()` call-site mismatch so the standard local
  demo dataset can be rebuilt successfully.
- Added consistent sale pricing across the catalog, checkout calculations, VAT,
  shipping thresholds, and stored order-item unit prices.
- Added the repo-scoped `$swap-product-card-photo` and
  `$apply-product-sale-pricing` skills with focused regression coverage.
- Replaced the heritage seal with an original simplified happy-person logo that
  expresses “Maligaya,” using golden-yellow arms and an ivory torso for clear
  contrast against the forest-green header while retaining the responsive
  left-aligned lockup, configured full company name, and right-aligned navigation.
- Added an Orders-list N+1 regression test that proves query count stays constant
  from one rendered order to ten while every item count still renders.
- Replaced the browser-native quantity `datalist` with an accessible storefront
  picker that expands in the card layout, preserves free numeric entry, and cannot
  cover the Add to cart button.
- Fixed the planted `DatabaseSeeder::seedOrders()` argument mismatch and verified
  fresh migration and seeding against an isolated SQLite database.
- Added a rehearsal learning record with the baseline, educational changes,
  verification evidence, teaching points, and restore boundaries.
- Added a generated catalog photo for live SKU `PP-1112` through the SKU-based
  product-photo resolver and extended focused coverage for the local asset.
- Replaced the generated image for live Classic Floor Mat SKU `YS-7893` with
  Mattes's public-domain photograph of a colorful textile doormat in Thailand.

### Current follow-up work

- The admin login feature remains planned but unimplemented; see the
  [admin login PRD](admin-login-prd.md).

## 2026-07-24

- `main` is synchronized with `origin/main`.
- Added a shared Markdown Kanban board for work that needs to remain visible between
  development sessions.
- Fixed the live Orders search and added feature-test coverage for the search
  response.
- Standardized the customer-facing company name as `SMCT`, sourced through
  `config('app.name')`.
- Expanded `AGENTS.md` with guidance for branding, environment-backed operational
  identifiers, production data, generated files, local checks, dependencies, and
  deployment verification.
- Consolidated project documentation under `docs/` and linked it from the root
  README.

### Follow-up work recorded at the time

- Fix the `DatabaseSeeder::seedOrders()` argument mismatch so a fresh
  `php artisan migrate --seed` succeeds.
- Decide whether the reusable `<x-card>` image prop should replace the inline product
  image markup.
- Add a regression test that protects the Orders list from N+1 queries.
- The admin login feature remains planned but unimplemented; see the
  [admin login PRD](admin-login-prd.md).

## 2026-07-20

- Added the admin login product requirements specification.
- Merged the backend planning work into `main`.
- Renamed the storefront to `SMCT`.
