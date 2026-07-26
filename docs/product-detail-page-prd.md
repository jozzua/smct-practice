# Product Requirements Spec: Product Detail Page

Board item: [[kanban]] → Todo: "Product detail page at /products/{sku}"

## Objective

Give every product a linkable detail page at `/products/{sku}`, so a card click leads
somewhere and a product can be shared by URL.

## Context

The catalog is the only product surface; cards are dead ends. The route key is the
**SKU** — the repo's one stable public identifier (names are not identifiers here,
and SKUs can contain spaces, so URL-encoding is part of the contract:
`/products/EF%202002` must resolve).

## Scope

- Route `GET /products/{sku}` with the product resolved **by SKU** (route-model
  binding on the `sku` column or an explicit lookup).
- Detail view: photo via `Product::imageUrl()`, name, SKU, price with the existing
  sale-price presentation, description, and the existing quantity/add-to-cart
  controls.
- Catalog cards link to their detail page (URL-encoded SKU).
- Unknown SKU → 404.

Out of scope: related products, reviews, stock display, breadcrumbs, SEO metadata.

## Requirements

### 1. Routing by SKU

`/products/{sku}` resolves the product by exact SKU; a SKU with a space works when
URL-encoded; an unknown SKU returns 404 (not a 500, not an empty page).

### 2. Detail view conventions

The page reuses the photo resolver and the sale-price display rules — no second
implementation of either. Layout extends the shared app layout.

### 3. Catalog linking

Each card's title (or image) links to the product's detail URL built with
`rawurlencode`/`urlencode` — never raw SKU interpolation.

## Definition of Done

- [ ] Feature tests: detail page renders (200) with name/SKU/price/photo URL in the
      response; **spaced SKU** resolves via its encoded URL; unknown SKU → 404;
      catalog page contains correctly encoded detail links.
- [ ] No inline `<img>` added to the catalog view (card contract intact).
- [ ] Full backend suite run (`composer test`); unrelated failures reported.
- [ ] PR from a branch, board line moved per the kanban workflow.
