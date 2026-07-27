# Rehearsal Learning Record

Updated: 2026-07-27 (Asia/Tokyo)

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

### `1f413e4` — Add sale-pricing behavior and its reusable skill

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

### `3defc82` — Replace the Classic Desk Lamp image

Status: implemented and verified locally.

- Selected the product by SKU `XK-0093`, not by the display name.
- Downloaded `Black desk lamp.jpg` from Wikimedia Commons:
  `https://commons.wikimedia.org/wiki/File:Black_desk_lamp.jpg`.
- The source file is dedicated to the public domain under CC0 1.0 and may be
  modified or used commercially without permission.
- Saved the original 2,444 × 2,447 grayscale JPEG as
  `public/images/products/XK-0093.jpeg`.
- Reused `Product::imageUrl()` without adding a URL array, SKU conditional, model
  edit, or Blade edit.
- Extended `ProductCardPhotoTest` to cover both local SKU images and one fallback
  SKU.

#### Teaching points

1. **Search with licensing in mind.** “Find an image online” is not only a visual
   search; suitability includes permission to reuse, source provenance, resolution,
   and crop compatibility.
2. **Prefer a convention over another code branch.** Once
   `{rawurlencode(SKU)}.jpeg` is established, adding the second product image should
   require only the asset and regression coverage.
3. **Test the extension point.** Verify the new local image, the earlier local image,
   and the remote fallback together so extending the convention does not narrow it.

At the start of the logo exercise, `main` and `origin/main` point to `1f413e4`;
the current `codex/replace-classic-desk-lamp-image` branch and its upstream point to
`3defc82`.

## Current educational exercise

### Replace the live Classic Airpot photo

Status: implemented.

- Selected the current local product by stable SKU `CQ-8868`; it is product ID `6`
  in the ignored seeded SQLite database.
- Reused Sarah Joy's unchanged 3,456 × 5,184 photograph
  [Hot pot (6850557477).jpg](https://commons.wikimedia.org/wiki/File:Hot_pot_(6850557477).jpg)
  from Wikimedia Commons.
- The photograph shows a vintage Japanese Peacock pump pot and is licensed under
  [CC BY-SA 2.0](https://creativecommons.org/licenses/by-sa/2.0/).
- Saved the original file unchanged as `public/images/products/CQ-8868.jpeg`.
- Reused `Product::imageUrl()` and the `{rawurlencode(SKU)}.jpeg` convention without
  adding a URL list, SKU conditional, model edit, seeder edit, or Blade edit.
- Extended `ProductCardPhotoTest` to cover the live SKU alongside the existing
  local SKU images and deterministic remote fallback.

#### Verification

- Source-image inspection — passed: the pump lid, carrying handle, and floral
  vintage body remain clear in the portrait composition.
- `php artisan test tests/Feature/ProductCardPhotoTest.php` — passed:
  1 test, 27 assertions.
- `vendor/bin/pint --test tests/Feature/ProductCardPhotoTest.php` — passed.
- `composer test` — passed: 21 tests passed, 1 skipped, and 121 assertions.
- `npm run build` — could not run in this local environment because the optional
  `@rolldown/binding-win32-x64-msvc` package is missing under Node `20.17.0`.
- In-app browser inspection — confirmed the unique `CQ-8868` card loads
  `/images/products/CQ-8868.jpeg`, renders the 3,456 × 5,184 source at 106 × 106,
  and preserves alt text `Classic Airpot sample photo`.

### Replace ad-hoc local products with the standard seeded dataset

Status: implemented and verified locally.

- The ignored local SQLite database started with 21 products, including the 20
  ad-hoc mock products and the local-only `NL-8805` browser-verification record;
  it had no customers, orders, or users.
- Fixed the documented `DatabaseSeeder::seedOrders()` call-site mismatch by passing
  the already-created Dr. Arnulfo Reynolds record as the required third argument.
- The intended local reset is `php artisan migrate:fresh --seed`, which replaces all
  local database contents and invalidates local sessions without touching production.
- `php artisan migrate:fresh --seed --force` completed successfully and rebuilt the
  local database with 40 products, 600 customers, 3,001 orders, 7,510 order items,
  and one demo staff account.

#### Verification

- `php artisan test tests/Feature/DatabaseSeederTest.php` — passed:
  1 test, 2 assertions.
- `vendor/bin/pint --test database/seeders/DatabaseSeeder.php` — passed.
- `composer test` — passed: 21 tests passed, 1 skipped, and 117 assertions.
- Direct database counts confirmed the standard dataset and at least one paid order
  for customer `arnulfo.reynolds@example.com`.
- In-app browser inspection confirmed the Products page renders all 40 seeded
  product cards.
- Because `ProductFactory` generates random SKUs, this seed does not currently
  contain `GV-9802`, `NL-8805`, `XH-5832`, or `XK-0093`. Their local image assets
  remain in the working tree but are not selected by the current seeded records.

### Replace the live Classic Gas Stove photo

Status: implemented.

- Selected the live product by stable SKU `NL-8805`, not by its display name,
  database ID, or catalog position.
- Downloaded Alf van Beem's 2,229 × 2,972 photograph
  [Old gas stove Teknikens och Sjöfartens hus, Science and Maritime House.JPG](https://commons.wikimedia.org/wiki/File:Old_gas_stove_Teknikens_och_Sj%C3%B6fartens_hus,_Science_and_Maritime_House.JPG)
  from Wikimedia Commons.
- The photograph shows an old gas stove displayed at Malmö Museer's Science and
  Maritime House and is dedicated to the public domain under
  [CC0 1.0](https://creativecommons.org/publicdomain/zero/1.0/).
- Saved the original file unchanged as `public/images/products/NL-8805.jpeg`.
- Reused `Product::imageUrl()` and the `{rawurlencode(SKU)}.jpeg` convention without
  adding a URL list, SKU conditional, model edit, or Blade edit.
- Extended `ProductCardPhotoTest` to cover the new local image alongside the
  existing local SKU images and deterministic remote fallback.
- The ignored local SQLite database did not contain the requested SKU, so a
  local-only `Classic Gas Stove` rehearsal record was created as product ID `21`
  with SKU `NL-8805`; the local catalog count changed from `20` to `21`.
- The later standard database reset removed that local-only record while preserving
  the source-controlled photo integration work.

#### Verification

- Source-image inspection — passed: the complete vintage stove, three burners,
  control knobs, and oven door are clear in the portrait composition.
- `php artisan test tests/Feature/ProductCardPhotoTest.php` — passed:
  1 test, 23 assertions.
- `vendor/bin/pint --test tests/Feature/ProductCardPhotoTest.php` — passed.
- `npm run build` — could not run in this local environment: Node `20.17.0` is
  below Vite's required `20.19.0`, and the optional
  `@rolldown/binding-win32-x64-msvc` package is missing.
- `composer test` — 20 passed, 1 skipped, and 1 errored on the existing
  `DatabaseSeeder::seedOrders()` argument mismatch; 115 assertions completed.
- Local image response — confirmed `/images/products/NL-8805.jpeg` resolves as the
  SKU-specific image.
- In-app browser inspection — confirmed one `NL-8805` card renders the 2,229 ×
  2,972 source at 106 × 106 with alt text `Classic Gas Stove sample photo`.

### Simplify the Maligaya header logo

Status: implemented and verified locally.

- Generated an original stylized happy-person mark with the built-in
  image-generation tool to express the meaning of “Maligaya.”
- Used a compact joyful silhouette, uplifted golden-yellow arms, and an ivory torso
  that contrast clearly with the existing forest-green header; retained copper for
  the small smile.
- Generated on a flat magenta chroma-key background, removed the background locally,
  and saved a 1254 × 1254 RGBA PNG at the stable existing asset path
  `public/images/brand/maligaya-trade-seal.png`.
- Kept the full company name as live `config('app.name')` text instead of baking it
  into the image.
- Reused the existing left-aligned header lockup, so replacing one shared asset
  updates every header instance without duplicating markup or URLs.
- Used an empty image `alt` because the adjacent link text already provides the
  accessible company name.
- Kept `BrandingTest` focused on the stable integration contract: configured name,
  local logo file, logo class, rendered asset URL, and header order.

#### Teaching points

1. **Translate meaning into shape.** “Maligaya” means joyful or happy, so a smiling
   person with raised arms communicates the brand without relying on initials or
   decorative complexity.
2. **Design for the real display size.** A header logo is judged at 56 × 56 pixels;
   one person, a clear smile, strong contrast, and generous negative space matter
   more than detail that only works in the full-resolution source.
3. **Keep brand text in HTML.** The image remains language-neutral and decorative,
   while `config('app.name')` stays searchable, accessible, configurable, and
   testable.
4. **Preserve a stable asset contract.** Replacing the file at the shared asset path
   updates every layout instance consistently without adding URL lists or duplicating
   template logic.
5. **Verify beyond generation.** Inspect transparency and edges, run the focused
   integration test and frontend build, then check the natural and rendered sizes in
   the real browser header.

#### Generation prompt summary

```text
Create a compact website-header logo for “Maligaya”: one simplified, geometric,
vector-style happy person with a circular smiling head and uplifted open arms.
Use bright golden yellow for the raised arms, warm copper for the smile, and ivory
for the head and torso so the full silhouette contrasts with the forest-green
header. Keep it centered, text-free, isolated, and readable at 56 × 56 pixels,
with no seal, crest, border, mockup, or watermark.
```

#### Verification

- Transparent logo inspection — passed: 1254 × 1254 RGBA output, transparent
  background, crisp joyful-person silhouette, and no visible chroma fringe.
- `php artisan test tests/Feature/BrandingTest.php` — passed:
  1 test, 6 assertions.
- `npm run build` — passed. Vite emitted only the existing optional `fontaine`
  optimization notice.
- `composer test` — 18 passed, 1 skipped, and 1 errored on the existing
  `DatabaseSeeder::seedOrders()` argument mismatch; 97 assertions completed.
- Rendered local HTML — confirmed the full configured name and logo asset URL appear
  together in the header brand link.
- Local logo response — `200 OK`, `image/png`, 262,045 bytes.
- In-app visual browser inspection — confirmed the 1254 × 1254 source loads at
  56 × 56 in the `rgb(29, 111, 92)` upper-left header, the golden-yellow arms and
  ivory lower body remain visible against that green background, the configured
  company name follows it, and navigation remains on the right.

### Guard the Orders list against N+1 regressions

Status: implemented and verified locally.

- Added a focused test to `OrdersIndexTest` without changing `OrderController`.
- Created one order with two items, measured the rendered Orders-index query count,
  then added nine more orders with two items each and measured again.
- Asserted that query count remains constant as the number of rendered orders grows.
- Capped the request at two queries: one for orders with item-count subqueries and
  one for eager-loaded customers.
- Verified all ten rows render their item count so removing `withCount('items')`
  cannot produce a misleading lower query count.
- Kept the test independent of timing and production-scale seeded data.

#### Verification

- `php artisan test tests/Feature/OrdersIndexTest.php` — passed:
  6 tests, 29 assertions.
- `vendor/bin/pint --test tests/Feature/OrdersIndexTest.php` — passed after a
  mechanical class-method spacing fix.
- `composer test` — 19 passed, 1 skipped, and 1 errored on the existing
  `DatabaseSeeder::seedOrders()` argument mismatch; 103 assertions completed.
- The intentional `DatabaseSeeder::seedOrders()` mismatch remains untouched.

### Replace the native quantity dropdown

Status: implemented and verified locally.

- Replaced the unstyleable `<datalist>` popup with an in-page quantity picker.
- Preserved the number input so customers can still enter any positive quantity.
- Added eight useful preset buttons inside a semantic listbox, with selected-state,
  Escape-key, outside-click, and focus-return behavior.
- Made the menu expand inside the product card instead of floating over the
  Add to cart button.
- Added `ProductQuantityPickerTest` to protect the accessible picker contract and
  prevent the native datalist from returning.

#### Teaching points

1. **Know the browser boundary.** A `<datalist>` popup is rendered by browser and
   operating-system chrome; its colors and placement cannot be made reliably
   consistent with the application through CSS.
2. **Preserve the flexible path.** Quick presets should accelerate common choices,
   not replace the editable number input or narrow the valid quantities accepted by
   the backend.
3. **Fix geometry, not just color.** Expanding the options in document flow removes
   overlap by construction, rather than relying on a fragile z-index or viewport
   assumption.
4. **Test semantics and interaction.** The feature test protects the server-rendered
   contract, while browser verification checks selection, focus, layout, and visual
   styling in the real page.

#### Verification

- `php artisan test tests/Feature/ProductQuantityPickerTest.php tests/Feature/CheckoutTest.php`
  — passed: 5 tests, 27 assertions.
- `vendor/bin/pint --test tests/Feature/ProductQuantityPickerTest.php` — passed.
- `npm run build` — passed. Vite emitted only the existing optional `fontaine`
  optimization notice.
- `composer test` — 20 passed, 1 skipped, and 1 errored on the existing
  `DatabaseSeeder::seedOrders()` argument mismatch; 111 assertions completed.
- In-app browser interaction — confirmed the custom menu has a light storefront
  surface, does not overlap the cart button, selecting `25` updates and focuses the
  number input, and the menu closes with the selected option marked.
- Repository investigation found no `dev/` directory or separate TODO notes; the
  Kanban board remains the source of truth for follow-up work.

## Verification record

### Display-name exercise

- `php artisan optimize:clear` — passed.
- `php artisan about --only=environment` — resolved the application name as
  `Maligaya Trading Company`.
- `php artisan test tests/Feature/BrandingTest.php` — passed: 1 test, 2 assertions.
- `composer test` — 17 passed, 1 skipped, and 1 errored on the intentionally planted
  `DatabaseSeeder::seedOrders()` argument mismatch.

The planted seeder mismatch was not changed. The Orders-list eager-loading
implementation was left untouched and is now protected by a query-count regression
test.

### Product-photo exercise

`ProductCardPhotoTest` was included in the focused pre-commit run below and passed.

#### Classic Desk Lamp image

- `php artisan test tests/Feature/ProductCardPhotoTest.php` — passed:
  1 test, 19 assertions.
- `vendor/bin/pint --test tests/Feature/ProductCardPhotoTest.php` — passed.
- `composer test` — 18 passed, 1 skipped, and 1 errored on the existing
  `DatabaseSeeder::seedOrders()` argument mismatch; 93 assertions completed.
- Browser verification — confirmed the Classic Desk Lamp card for SKU `XK-0093`
  loads `/images/products/XK-0093.jpeg` at 2,444 × 2,447 and remains legible within
  the circular product crop.

#### Premium Gas Stove image

Status: implemented locally for SKU `PP-1112`.

- Used `$swap-product-card-photo` to keep the change on the SKU-based resolver path.
- Generated a clean catalog product photo of a premium two-burner gas stove.
- Saved the final JPEG at `public/images/products/PP-1112.jpeg`, so
  `Product::imageUrl()` resolves the exact live SKU without model or view changes.
- Extended `ProductCardPhotoTest` to assert the `PP-1112` asset exists and renders
  through the catalog card alongside the previous local images and fallback SKU.

#### Verification

- `php artisan test tests/Feature/ProductCardPhotoTest.php` with the project-local
  PHP runtime after rebasing onto current `main` — passed: 1 test, 27 assertions.
- `composer test` with the project-local PHP runtime — 20 passed, 1 skipped, and
  1 errored on the existing `DatabaseSeeder::seedOrders()` argument mismatch;
  119 assertions completed.
- Repository investigation found no `storage/logs/laravel.log`, no repo-local
  `dev/` directory, and no separate TODO notes; the Kanban board remains the
  source of truth for follow-up work.

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

- To return the active branch to the rehearsal baseline while preserving history,
  review the educational commits `3defc82`, `1f413e4`, `b350e75`, `f701fbb`,
  `0c58f79`, and `377f510` in reverse chronological order.
- Treat `b350e75` and the ignored local `.env` value as separate restore targets.
  If restoring the display name to `SMCT`, keep `SESSION_COOKIE`, `CACHE_PREFIX`,
  and `REDIS_PREFIX` unchanged.
- Treat the header-logo asset, layout, CSS, branding test, and documentation as one
  restore unit.
- Do not use seeders as a production data migration, and do not access production
  infrastructure as part of rehearsal restoration.
