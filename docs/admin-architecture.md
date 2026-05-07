# Admin Architecture

This scaffold keeps the admin system isolated and traceable so future developers can locate issues quickly.

## Folder layout

- `config/`
  Shared bootstrap, app settings, auth helpers, and database connection.
- `admin/`
  Admin entry points only.
- `admin/includes/`
  Shared layout pieces for the dashboard shell.
- `admin/modules/`
  Feature-focused admin screens:
  `dashboard`, `content`, `donations`, `admins`, `settings`.
- `database/`
  SQL schema and future seed files.
- `assets/css/admin.css`
  Dashboard-only styles separated from the public theme.

## Current behavior

- Login is session-based and uses the seeded scaffold account from `config/app.php`.
- Database connection helpers are ready in `config/database.php`.
- Admin views are routed through `admin/index.php?page=...`.

## Default scaffold login

- Email: `admin@graciouscharity.org`
- Password: `ChangeMe123!`

Change this immediately once the database-backed auth flow is implemented.

## Recommended next implementation steps

1. Replace seeded admin auth with database-backed password hashes.
2. Move public-site hardcoded text into `pages` and `content_blocks`.
3. Add CRUD screens for gallery, partners, programmes, and FAQs.
4. Add Paystack and Stripe webhook handlers and persist payloads into `payment_transactions`.
5. Add middleware for role-based authorization by module.
