# Project Instructions

## Branding and company name

- The current customer-facing company name is `SMCT`.
- Use `config('app.name')` for customer-visible company-name text in Blade views. Do not hard-code the company name in individual pages.
- Before changing branding, search the repository for the current and proposed names. Review configuration, Blade views, email templates, metadata, documentation, and tests.
- `APP_NAME` is the source for Laravel's application name. Updating `.env.example` or a default in `config/app.php` does **not** update an existing local or production `.env` value; an actual environment value overrides both.

## Operational impact of APP_NAME

- In this application, the default session-cookie name, cache prefix, and Redis prefix are derived from `APP_NAME`. A display-name change can therefore invalidate sessions or move cache namespaces.
- Treat `SESSION_COOKIE`, `CACHE_PREFIX`, and `REDIS_PREFIX` as operational identifiers. Do not change them as a side effect of a branding update. If a change is necessary, explain the user impact and obtain approval first.

## Production data and seeders

- Seeders initialize a fresh database. They do not modify an already-populated production database unless someone explicitly runs them, and they must not be used as a production data-migration mechanism.
- Do not assume a display name identifies a record. Before changing live data, inspect the current record and use its primary key or a confirmed stable unique identifier.
- To change an existing production record, prepare an idempotent data migration or a narrowly scoped maintenance command. State the exact records and intended before/after values, then obtain explicit approval before running it.
- When a requirement names a record but the requested final value, identifier, or user impact is ambiguous, stop and ask a concise clarifying question before making a production data change.
- Tests for a data change must assert the intended existing record or stable unique identifier. A test that only proves a fresh seed can create a similar record is insufficient.

## Deployment checklist

When a branding or environment-backed configuration change is approved for deployment:

1. Update the source-controlled defaults and every customer-visible usage.
2. Update the real deployment environment or `.env`; do not rely on `.env.example`.
3. On the target host, run `php artisan optimize:clear` followed by `php artisan config:cache`.
4. Verify the live page directly. Confirm the document title, header brand, page content, footer, and relevant response headers show the intended value.
5. For data changes, query the live record after deployment and verify its identifier and final values.
6. Do not claim the change is complete based only on Git history or a local environment.

Never access or change production infrastructure without explicit user authorization.
