# PHP Rebuild

This directory contains the shared-hosting PHP rebuild of the application. It keeps the existing PostgreSQL schema and replaces the Next.js runtime with a plain PHP stack that works on cPanel-style hosting.

## Structure

- `public/` front controller and `.htaccess`
- `app/Core/` router, database, session, and view bootstrap
- `app/Controllers/` route handlers
- `app/Repositories/` SQL access against the existing schema
- `app/Views/` server-rendered PHP templates

## Current routes

- `/` home
- `/about`
- `/terms`
- `/terms/show?id=...`
- `/login`
- `/signup`
- `/dashboard`
- `/assessments`
- `/assessments/create`
- `/assessments/show?id=...`
- `/products`
- `/products/show?id=...`
- `/nominate`
- `/certification/request`
- `/cart`
- `/checkout`
- `/admin`
- `/admin/manage?module=...`
- `/admin/form?module=...`
- `/partner`
- `/profile`

## Environment

Create `php-app/.env` with either `DATABASE_URL` or individual database settings:

```env
APP_NAME="ISO Compliance Hub"
APP_URL="https://your-domain.com"
APP_ENV=production
APP_DEBUG=false

DATABASE_URL="pgsql://dbuser:dbpassword@127.0.0.1:5432/iso_compliance_hub"
```

Or:

```env
DB_DRIVER=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=iso_compliance_hub
DB_USER=dbuser
DB_PASSWORD=dbpassword
```

## Deployment on shared hosting

Point the domain or subdomain document root to `php-app/public`.

If the host cannot change the document root, copy the contents of `php-app/public/` into the web root and keep the rest of `php-app/` one level above it.

## Next migration steps

1. Expand assessment scoring and report generation beyond the current basic scoring.
2. Port password reset and email verification.
3. Add dedicated CRUD screens for clauses, questions, messages, product images, and recommendations.
4. Port real checkout and payment integrations.
5. Port uploads and PDF reports.
