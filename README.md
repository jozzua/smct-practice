# SMCT - S1 practice project

A small, runnable Laravel storefront (products → cart → checkout → orders) used as the
working repo for Session 1. It exists so the team can practice prompting against real
application code. It is not a product and holds no real data — every customer, order,
and product is generated.

## What's inside

- Laravel 13, server-rendered Blade. No JavaScript build is needed to run it — the
  stylesheet is served straight from `public/css/app.css`.
- A neutral small-commerce domain: customers, products, orders, order items.
- Seeded demo data: ~3,000 orders across ~600 customers and 40 products.
- A handful of feature tests (`php artisan test`).

## Documentation

- [Development log](docs/development-log.md) — recent changes, current status, and
  known follow-up work.
- [Rehearsal learning record](docs/rehearsal-learning-record.md) — educational
  experiments, verification results, and restore points.
- [Kanban board](docs/kanban.md) — shared Todo, Doing, and Done items.
- [QA checklist](docs/qa-checklist.md) — the three QA altitudes, test-first
  workflow, and the reviewer checklist for every PR.
- [Admin login PRD](docs/admin-login-prd.md) — requirements for the planned admin
  access-control foundation.
- [Laravel release operations](docs/operate-laravel-releases.md) — approval-gated
  deployment, verification, evidence, and rollback guidance for DevOps work.

## Setup (about 5 minutes)

```bash
git clone <this-repo> && cd <this-repo>
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

Then open http://localhost:8000.

Demo staff login: `demo@example.com` / `password`.

## A note on the database

This repo uses **SQLite** so setup is a single file with no server to install. Our
production stack is **MySQL** — the migrations, Eloquent models, and query patterns
here are exactly what you'd write against MySQL; only the connection differs. Don't
lean on SQLite-specific behavior, and don't read anything into the engine choice.

## Where to look

| Area | Entry point |
| --- | --- |
| Product catalog + cart | `app/Http/Controllers/ProductController.php`, `CartController.php` |
| Checkout + totals | `app/Http/Controllers/CheckoutController.php` |
| Orders list (staff view) | `app/Http/Controllers/OrderController.php` |
| Accounts + profile | `SignupController.php`, `SessionController.php`, `ProfileController.php` |
| Views | `resources/views/` |
| Demo data | `database/seeders/DatabaseSeeder.php` |
