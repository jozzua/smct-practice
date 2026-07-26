---
name: swap-product-card-photo
description: Swap or revise catalog product-card photos in the SMCT Laravel repository while preserving the shared Blade card component and deterministic per-product images. Use for product catalog photo changes, product image merge-conflict resolution, or regressions involving `resources/views/products/index.blade.php` and `resources/views/components/card.blade.php`.
---

# Swap Product Card Photo

Preserve the repository's product-card contract: render photos through `<x-card>`, derive one distinct image from each product SKU, avoid hardcoded URL collections, and prove the result with a passing regression test.

## Workflow

1. Read `AGENTS.md`, `docs/development-log.md`, and `docs/kanban.md`. Inspect the current worktree and the product view, card component, product factory or seeder, and related feature tests before editing.
2. Identify products by exact `sku`. Do not use a display name, loop position, or database ID as a durable product selector. When a fixture or seeder must establish a product, create or update it by SKU.
3. Keep the catalog markup in `resources/views/products/index.blade.php` wrapped in `<x-card>`. Pass the photo with the component's `:image` and `:image-alt` props. Keep the actual `<img>` element in `resources/views/components/card.blade.php`; do not duplicate it in the catalog view.
4. Use one image URL template whose seed is `urlencode($product->sku)`. The existing convention is:

   ```blade
   <x-card
       :title="$product->name"
       :image="'https://picsum.photos/seed/' . urlencode($product->sku) . '/240'"
       :image-alt="$product->name . ' sample photo'"
   >
   ```

   Preserve a useful product-specific alt value. If the requested source changes, keep a single template or resolver and continue deriving the seed from SKU.
5. Never add an array or rotating list of image URLs, per-index assignment, or CRC/modulo selection. Do not replace `<x-card>` with a raw `.card` wrapper to make the photo change.
6. Add or update a focused feature test. Use at least two products with explicit, different SKUs and verify:

   - the catalog view invokes `<x-card>` and contains no inline `<img>`;
   - the view has one URL template, not a URL list;
   - the template uses the URL-encoded SKU;
   - the rendered response contains the expected distinct image URL and alt text for each product.

7. Run the focused test until it passes. Then run `composer test` as the repository-wide backend check. Do not finish without a passing focused test. Report any unrelated full-suite failure separately and do not claim that the full suite passed.

## Guardrails

- Make only the smallest photo-related change.
- Do not edit generated dependencies, built assets, logs, or session files.
- Do not change a production record. Seeders only initialize fresh databases; use a separately approved idempotent data migration or maintenance command for existing data.
- Do not change branding or environment-backed operational identifiers as part of a photo task.
