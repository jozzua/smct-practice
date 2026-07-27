# Product Requirements Spec: Catalog Search by Name or SKU

Board item: [[kanban]] → Todo: "Catalog search by name or SKU"

## Objective

Let a visitor filter the products page by typing part of a product name or SKU, so a
40-product catalog stops being scroll-only.

## Context

The catalog at `/products` renders every product as an `<x-card>`. There is no way to
narrow the list. Staff also use this page to answer "do we carry X?" — search is the
smallest feature that serves both audiences. SKUs in this catalog can contain spaces
(e.g. `EF 2002`), which has burned this repo before: URL-encoding is part of the spec,
not an afterthought.

## Scope

- A search input on the products index, submitting as a GET parameter (`?q=`).
- Case-insensitive partial match against product **name** OR **SKU**.
- An empty-result state with a clear message and a "clear search" link.

Out of scope: fuzzy matching, search-as-you-type/JS autocomplete, searching orders or
customers, pagination.

## Requirements

### 1. Search input

A labelled search field on the products index; the current query stays visible in the
field after submit.

Acceptance: submitting the form reloads `/products?q=...` with results filtered.

### 2. Matching

`q` matches partial, case-insensitive, against `name` or `sku`. Empty/whitespace `q`
returns the full catalog.

Acceptance: searching a fragment of a name and a fragment of a SKU each return the
expected product(s).

### 3. Card contract preserved

Results render through the existing `<x-card>` component with `imageUrl()` photos and
sale-price UI intact — no duplicated markup.

Acceptance: the rendered cards for search results are identical in structure to the
unfiltered page.

### 4. Empty state

No matches → a friendly message ("No products match “…”") plus a link back to the full
catalog.

## Definition of Done

- [ ] Feature test covers: match by name fragment, match by SKU fragment (including a
      SKU **with a space**, URL-encoded in the request), empty query = full list, and
      the empty-result state.
- [ ] Query is bound/escaped through Eloquent — no raw string interpolation into SQL.
- [ ] Full backend suite run (`composer test`); unrelated failures reported, not
      claimed as passing.
- [ ] PR from a branch, board line moved per the kanban workflow.
