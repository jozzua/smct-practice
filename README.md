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

## Dev Status Log

### 2026-07-20

- `main` contains the current storefront practice app and is ahead of `origin/main`
  by the local admin-login planning merge.
- Added the admin login product requirements spec at `docs/admin-login-prd.md`.
- Created a `backend-stuff` worktree/branch for backend planning and merged its PRD
  commit back into `main`.
- No admin login implementation has been built yet. The next backend step is to add
  the `is_admin` user flag, admin middleware, `/admin` route group, dashboard
  placeholder, navigation visibility, and feature tests described in the PRD.

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
