# Product Requirements Spec: Order Status Filter Tabs

Board item: [[kanban]] → Todo: "Order status filter tabs on the Orders index"

## Objective

Add All / Paid / Pending tabs to the Orders index so staff can work one status at a
time instead of scanning the full list.

## Context

The Orders index lists every order with live search. Status is already stored per
order; there is just no way to filter by it. This page also carries an **N+1
regression guard** (query-count test) — the filter must not undo that work.

## Scope

- Three tabs above the orders table: **All** (default), **Paid**, **Pending**,
  driven by a GET parameter (`?status=`).
- Active tab visually distinct; filter state survives alongside the existing search
  (filter + search compose, not compete).
- Each tab shows its order count.

Out of scope: new statuses, bulk status changes, saved views, pagination changes.

## Requirements

### 1. Filtering

`?status=paid|pending` scopes the query (Eloquent scope or where clause); anything
else (missing, empty, unknown value) falls back to All — never an error.

Acceptance: each tab shows only orders of that status; unknown values are harmless.

### 2. Composition with search

An active search query stays applied when switching tabs, and vice versa (both live
in the query string together).

### 3. Counts and performance

Tab counts are computed without per-row queries; the existing orders N+1 query-count
guard still passes with the filter active.

## Definition of Done

- [ ] Feature tests: default = all orders; paid-only; pending-only; unknown status
      falls back to all; search + filter combined returns the intersection.
- [ ] The N+1/query-count regression test passes unchanged (or is extended, with the
      count still pinned).
- [ ] Active-tab state and counts render correctly in the view.
- [ ] Full backend suite run (`composer test`); unrelated failures reported.
- [ ] PR from a branch, board line moved per the kanban workflow.
