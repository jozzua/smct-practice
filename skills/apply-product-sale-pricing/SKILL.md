---
name: apply-product-sale-pricing
description: Add or revise sale pricing in the SMCT Laravel storefront while keeping catalog presentation, cart totals, VAT, shipping thresholds, and stored order-item prices consistent. Use for struck-through list prices, percentage discounts, SKU-specific promotional prices, or checkout mismatches caused by displayed sale prices.
---

# Apply Product Sale Pricing

Implement sale pricing as business behavior, not a catalog-only decoration. Keep the stored list price available for comparison, derive the sale price once, and use that result everywhere a customer sees or pays a price.

## Workflow

1. Read `AGENTS.md`, `docs/rehearsal-learning-record.md`, `docs/development-log.md`, and `docs/kanban.md`. Inspect the worktree before editing and preserve unrelated rehearsal changes.
2. Search every use of `price_cents`, `unit_price_cents`, subtotal, VAT, and shipping thresholds across models, controllers, Blade views, seeders, and tests.
3. Confirm the pricing rule:

   - Identify exceptional products by exact SKU, never by display name or loop position.
   - Distinguish the stored list price from the derived sale price.
   - Clarify an ambiguous final price before changing persisted data.
   - Keep money as integer centavos. Use integer arithmetic such as `intdiv($priceCents, 2)` for 50% discounts.

4. Centralize the rule on the product model or a dedicated pricing service. Provide one method such as `salePriceCents()` and derive the displayed percentage from the list and sale prices. Do not repeat discount arithmetic in Blade templates or controllers.
5. Apply the same sale-price method to:

   - the catalog sale price;
   - checkout unit and line prices;
   - subtotal and VAT calculations;
   - the free-shipping threshold;
   - the `unit_price_cents` captured on the order item.

   Do not show a discounted price while charging the stored list price.
6. Render the list price with semantic `<del>` markup, the sale price as the primary amount, and a concise percentage-off label. Add reusable CSS classes instead of inline styling.
7. Add focused regression tests with at least two explicitly created products:

   - one exact SKU with a fixed promotional price;
   - one ordinary SKU using the general discount;
   - exact integer sale prices and discount percentages;
   - rendered `<del>`, sale-price, and discount-label output;
   - checkout subtotal, VAT, shipping, total, and stored order-item unit price.

8. Run the focused pricing and checkout tests until they pass. Run Pint for changed PHP, `npm run build` for Blade/CSS changes, and `composer test` for the full backend suite. Report unrelated pre-existing failures separately.
9. Reload the local catalog and verify representative special and general discounts in the browser. Confirm that the original, sale, and percentage values agree.
10. Update `docs/rehearsal-learning-record.md` with the exercise intent, affected files, design decisions, verification evidence, teaching points, and restore boundary.

## Production Boundary

Treat a computed sale layered over `price_cents` as reversible application behavior. If the requirement instead needs persisted campaign dates, editable promotions, reporting, or production-record changes, design an explicit schema and an idempotent data migration or maintenance command. State exact before/after records and obtain approval before touching production.

## Completion Checklist

- Select exceptions by SKU.
- Keep list and sale prices distinct.
- Use one sale-price calculation everywhere.
- Store the charged sale price on order items.
- Pass focused tests and the frontend build.
- Record full-suite limitations and teaching notes.
