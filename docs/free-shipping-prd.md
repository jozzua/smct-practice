# Product Requirements Spec: Honor the "Free Delivery ₱5,000+" Promise

Board item: [[kanban]] → Todo: "Honor the free-delivery-₱5,000+ promise at checkout"

## Objective

Make checkout behavior match the promise the site already makes: the products page
header says **"Free delivery on orders ₱5,000 and up"** — but checkout charges
shipping regardless of order size. Close the gap.

## Context

The requirement was already shipped — as copy. This ticket exists because the spec
was on the page all along and nobody wired it to the money path. Checkout currently
computes a subtotal (using **sale prices** where active), VAT, and a shipping fee.

**Spec decision to encode (not assume):** the ₱5,000 threshold applies to the
**post-sale-price subtotal, before VAT and before shipping**. If implementation
reveals a reason to change that basis, raise it on the PR — do not silently choose.

## Scope

- Waive the shipping fee when the qualifying subtotal is ≥ ₱5,000.
- Show the outcome on the checkout page: either "Free delivery" as the shipping line,
  or "Add ₱X more for free delivery" when below the threshold.

Out of scope: changing VAT rules, changing the threshold amount, promo codes,
changing the header copy.

## Requirements

### 1. Threshold logic

Subtotal (sale-priced, pre-VAT) ≥ ₱5,000 → shipping = ₱0. Below → existing fee
unchanged.

Acceptance: totals recompute correctly on both sides of the threshold, including
exactly ₱5,000 (qualifies).

### 2. Checkout messaging

Qualifying orders show "Free delivery" on the shipping line; non-qualifying orders
show the remaining amount needed, computed from the same basis.

Acceptance: the message and the charged amount can never disagree (both derive from
one computation).

### 3. Stored orders stay consistent

Order records store the shipping actually charged (₱0 when waived).

## Definition of Done

- [ ] Feature tests: below threshold (fee charged + "add ₱X more" shown), at exactly
      ₱5,000 (free + label shown), above threshold, and sale-price interaction (a
      cart that qualifies only because of sale prices).
- [ ] The threshold and basis live in one place (config or a single method) — not
      duplicated in the view.
- [ ] Full backend suite run (`composer test`); unrelated failures reported.
- [ ] PR from a branch, board line moved per the kanban workflow.
