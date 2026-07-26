# Development Log

This log records meaningful repository changes and the current development state.
The [Kanban board](kanban.md) is the source of truth for active work.

## 2026-07-27

- Restored the original customer-facing name, `Maligaya Trading Company`, through
  `APP_NAME` and `config('app.name')` while preserving the existing session, cache,
  and Redis identifiers.
- Established `Product::imageUrl()` as the single SKU-based product-photo resolver,
  with local images for SKUs `XH-5832` and `XK-0093` and a deterministic fallback.
- Added consistent sale pricing across the catalog, checkout calculations, VAT,
  shipping thresholds, and stored order-item unit prices.
- Added the repo-scoped `$swap-product-card-photo` and
  `$apply-product-sale-pricing` skills with focused regression coverage.
- Generated an original Maligaya Trade Seal and added it to a responsive,
  left-aligned header lockup before the configured full company name, while keeping
  navigation on the right.
- Added a rehearsal learning record with the baseline, educational changes,
  verification evidence, teaching points, and restore boundaries.

### Current follow-up work

- Fix the intentionally planted `DatabaseSeeder::seedOrders()` argument mismatch so
  a fresh `php artisan migrate --seed` succeeds.
- Add a regression test that protects the intentionally planted Orders-list N+1
  exercise.
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
