# Rehearsal Learning Record

Updated: 2026-07-26 (Asia/Tokyo)

## Purpose

This repository is being changed for SMCT AI-development-tools training. The work
below is educational and may be restored so the developers can repeat the exercises
themselves. Do not reset, revert, discard, or force-push any of it without explicit
approval.

## Rehearsal baseline

The clean pre-exercise baseline is commit `1d6f515`:

```text
1d6f515 Consolidate project documentation under docs
```

This baseline includes the approved README and documentation reorganization. It is
the preferred comparison point for the Session 2 exercises. The preceding
application commit is `7207779`.

## Committed educational exercises

### `377f510` — Add the product-photo skill

- Added the repo-scoped `$swap-product-card-photo` skill.
- Added a focused `ProductCardPhotoTest`.
- Teaching contract: keep `<x-card>`, select products by SKU, avoid hardcoded URL
  lists, and finish with a passing focused test.

### `0c58f79` — Use local product images with a seeded fallback

- Added `public/images/products/XH-5832.jpeg`, a local image for SKU `XH-5832`.
- Added `Product::imageUrl()` as the single image resolver.
- Changed the products view to pass `$product->imageUrl()` into `<x-card>`.
- Kept a deterministic Picsum fallback based on `rawurlencode($product->sku)`.
- Updated `ProductCardPhotoTest` for both the local image and fallback behavior.

### `f701fbb` — Synchronize the skill with the resolver

- Updated `$swap-product-card-photo` to teach the `Product::imageUrl()` convention.
- Documented the `{rawurlencode(SKU)}.jpeg` local-file naming rule.

### `b350e75` — Restore the original storefront display name

- Restored the original `Maligaya Trading Company` display name in `.env.example`,
  `config/app.php`, and the current-name rule in `AGENTS.md`.
- Kept `SESSION_COOKIE`, `CACHE_PREFIX`, and `REDIS_PREFIX` pinned to their existing
  `smct-*` operational identifiers.
- Updated the ignored local `.env` separately so the rehearsal site resolves the
  restored name.

Before the current pricing snapshot is committed, `main`, `origin/main`, and `HEAD`
all point to `b350e75`.

## Current uncommitted educational exercise

### Add sale-pricing behavior and its reusable skill

Status: implemented and verified locally.

This exercise adds a fixed promotional price for one SKU and a general 50% sale
without overwriting the stored list prices:

- `app/Models/Product.php` adds a fixed `129900`-cent sale price for airpot SKU
  `XH-5832`, applies a 50% price to other products, and calculates the displayed
  discount percentage.
- `app/Http/Controllers/CheckoutController.php` calculates order totals and stores
  order-item unit prices from `salePriceCents()`.
- `resources/views/products/index.blade.php` displays original price, sale price,
  and percentage-off text.
- `resources/views/checkout/show.blade.php` displays original and sale unit prices
  and calculates the displayed line total from the sale price.
- `public/css/app.css` styles the original price, sale price, and discount badge.
- `tests/Feature/CheckoutTest.php` updates checkout expectations for sale pricing.
- `tests/Feature/ProductPricingTest.php` is a new focused test for the fixed airpot
  offer and the general 50% discount.
- `skills/apply-product-sale-pricing/` captures the workflow as a reusable Codex
  skill.

Preserve these files as one restore unit unless the user explicitly chooses a
different pricing design.

#### Teaching points

1. **Resolve intent before coding.** “Apply a discount” can mean a visual label, a
   checkout price, or a persisted catalog change. The exercise explicitly treats
   the stored value as the list price and derives the charged sale price.
2. **Use stable identifiers.** The special offer targets SKU `XH-5832`, not the
   product name “Classic Airpot,” its database ID, or its position on the page.
3. **Create one source of pricing truth.** `Product::salePriceCents()` prevents the
   catalog, checkout, VAT, shipping, and order history from calculating different
   amounts.
4. **Do not build a cosmetic discount.** A struck-through price is misleading if
   checkout still charges the original value. The controller and stored order-item
   unit price must use the same sale method as the UI.
5. **Represent money as integers.** Prices remain in centavos; `intdiv()` applies
   the general 50% rule without floating-point currency errors.
6. **Preserve both meanings.** Keeping `price_cents` as the list price makes the
   `<del>` comparison and reversal straightforward while `salePriceCents()` supplies
   the active transactional amount.
7. **Test downstream consequences.** A price change affects VAT, free-delivery
   qualification, order totals, and the historical unit price stored on each order
   item—not just text in a Blade view.
8. **Verify in layers.** Focused tests establish behavior, Pint checks PHP style,
   the frontend build checks Blade/CSS integration, the full suite reveals broader
   regressions, and browser inspection confirms the actual hierarchy and values.
9. **Separate new failures from planted failures.** The known seeder argument
   mismatch remains unrelated; it is reported rather than silently fixed during a
   pricing exercise.
10. **Keep the production boundary explicit.** A real promotion system may need
    campaign dates, editable discounts, reporting, and an approved data migration.
    The rehearsal deliberately uses reversible application logic instead.
11. **Turn successful work into a reusable skill.** The first implementation
    discovers the hidden dependencies; the skill captures that proven sequence so a
    later Codex run starts with the right search surface, guardrails, and tests.
12. **Separate execution guidance from teaching context.** Keep `SKILL.md` compact
    and imperative so it is efficient at runtime. Keep rationale, evidence, restore
    points, and classroom discussion in this learning record.

## Verification record

### Display-name exercise

- `php artisan optimize:clear` — passed.
- `php artisan about --only=environment` — resolved the application name as
  `Maligaya Trading Company`.
- `php artisan test tests/Feature/BrandingTest.php` — passed: 1 test, 2 assertions.
- `composer test` — 17 passed, 1 skipped, and 1 errored on the intentionally planted
  `DatabaseSeeder::seedOrders()` argument mismatch.

The planted seeder mismatch was not changed. The planted Orders-list N+1 was also
left untouched.

### Product-photo exercise

`ProductCardPhotoTest` was included in the focused pre-commit run below and passed.

### Sale-pricing exercise

- `php artisan test tests/Feature/BrandingTest.php tests/Feature/ProductPricingTest.php tests/Feature/CheckoutTest.php tests/Feature/ProductCardPhotoTest.php`
  — passed: 7 tests, 47 assertions.
- `vendor/bin/pint --test app/Models/Product.php app/Http/Controllers/CheckoutController.php tests/Feature/ProductPricingTest.php tests/Feature/CheckoutTest.php tests/Feature/ProductCardPhotoTest.php`
  — passed.
- `npm run build` — passed. Vite emitted only the existing optional `fontaine`
  optimization notice.
- `composer test` — 18 passed, 1 skipped, and 1 errored on the existing
  `DatabaseSeeder::seedOrders()` argument mismatch; 89 assertions completed.
- Browser verification — confirmed SKU `XH-5832` displays
  `₱7,161.00 → ₱1,299.00` with `82% off`, while representative ordinary SKUs
  display exact 50% reductions.

### Sale-pricing skill

- Skill created at `skills/apply-product-sale-pricing/`.
- `quick_validate.py skills/apply-product-sale-pricing` — passed.
- Focused post-skill regression run — passed: 5 pricing/checkout tests,
  30 assertions.
- The skill contains only runtime workflow and guardrails; this record retains the
  longer teaching narrative.

## Restore targets

Before restoring anything, verify `git status --short --branch`, preserve this
record, and obtain explicit approval.

- To return committed exercise code to the rehearsal baseline while preserving
  history, review and revert `f701fbb`, `0c58f79`, and `377f510` in reverse
  chronological order.
- Treat `b350e75` and the ignored local `.env` value as separate restore targets.
  If restoring the display name to `SMCT`, keep `SESSION_COOKIE`, `CACHE_PREFIX`,
  and `REDIS_PREFIX` unchanged.
- Treat the uncommitted sale-pricing files as a separate restore unit; confirm the
  test run and intended checkpoint before changing them.
- Do not use seeders as a production data migration, and do not access production
  infrastructure as part of rehearsal restoration.
