# Kanban

A simple shared board for this repo. Move a line between sections in a pull request —
one line per task, newest at the top of each section.

## Todo

- Fix `DatabaseSeeder::seedOrders()` — it is called with 2 arguments but the signature
  requires 3, so `php artisan migrate --seed` currently fails on a fresh database.
- Add a test that guards the orders list against re-introducing an N+1 query.

## Doing

- (nothing in progress — add your name and the task when you pick something up)

## Done

- Maligaya Trade Seal added to the responsive upper-right header lockup beside the
  configured full company name.
- Rehearsal learning record added with educational checkpoints, verification
  evidence, and restore boundaries.
- Sale pricing centralized and applied consistently to the catalog, checkout, VAT,
  shipping, and stored order-item prices.
- Product photos resolved through `<x-card>` and `Product::imageUrl()`, with local
  assets selected by SKU and a deterministic fallback.
- Quantity input combined with a quick picker on the products page.
- Live search on the Orders index, with seeded records and tests.
- Company display name sourced through `config('app.name')` and restored to
  Maligaya Trading Company.
- `AGENTS.md` written so the branding, seeder and operational rules survive between tasks.

## How to use this

1. Pick a line from **Todo** and move it to **Doing** with your name.
2. Open a branch, do the work, open a pull request.
3. When it merges, move the line to **Done** and delete your name.
