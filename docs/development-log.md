# Development Log

This log records meaningful repository changes and the current development state.
The [Kanban board](kanban.md) is the source of truth for active work.

## 2026-07-27

- Replaced the catalog photo for Classic Flat Iron SKU `LI-8867` with a CC0
  museum photo while preserving the shared SKU-based image resolver.
- Restored the original customer-facing name, `Maligaya Trading Company`, through
  `APP_NAME` and `config('app.name')` while preserving the existing session, cache,
  and Redis identifiers.
- Established `Product::imageUrl()` as the single SKU-based product-photo resolver,
  with local images for SKUs `XH-5832` and `XK-0093` and a deterministic fallback.
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
