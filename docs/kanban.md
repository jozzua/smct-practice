# Kanban

A simple shared board for this repo. Move a line between sections in a pull request —
one line per task, newest at the top of each section.

## Todo

- Catalog search by name or SKU — spec: [[catalog-search-prd]]
- Honor the free-delivery-₱5,000+ promise at checkout — spec: [[free-shipping-prd]]
- Product detail page at /products/{sku} — spec: [[product-detail-page-prd]]
- Order status filter tabs on the Orders index — spec: [[order-status-filter-prd]]

## Doing

- Replace the catalog photo for product SKU `LI-8867` with a reusable classic
  flat-iron image. — Codex

## Done

- Real product photo added for live SKU `PP-1112` by invoking
  `$swap-product-card-photo`.
- Live Classic Airpot SKU `CQ-8868` given a real vintage pump-pot photo through
  `$swap-product-card-photo`.
- `DatabaseSeeder::seedOrders()` call fixed so a fresh standard demo seed completes.
- Live catalog SKU `NL-8805` given a real classic-gas-stove photo by invoking
  `$swap-product-card-photo`.
- Native quantity dropdown replaced with an accessible storefront-styled preset
  picker that does not overlap the Add to cart button.
- Orders-list N+1 regression test added with a constant query-count guard and rendered
  item-count coverage.
- Maligaya logo simplified to a joyful person in the responsive upper-left header
  lockup, before the configured full company name, with navigation retained on the
  right.
- Rehearsal learning record added with educational checkpoints, verification
  evidence, and restore boundaries.
- Sale pricing centralized and applied consistently to the catalog, checkout, VAT,
  shipping, and stored order-item prices.
- Product photos resolved through `<x-card>` and `Product::imageUrl()`, with local
  assets selected by SKU and a deterministic fallback.
- Live search on the Orders index, with seeded records and tests.
- Company display name sourced through `config('app.name')` and restored to
  Maligaya Trading Company.
- `AGENTS.md` written so the branding, seeder and operational rules survive between tasks.

## How to use this

1. Pick a line from **Todo** and move it to **Doing** with your name.
2. Open a branch, do the work, open a pull request.
3. When it merges, move the line to **Done** and delete your name.
