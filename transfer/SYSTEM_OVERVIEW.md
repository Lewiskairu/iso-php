# PHP Rebuild System Overview

## Purpose

This directory is the handover package for rebuilding the application from Next.js into a shared-hosting-friendly PHP application while preserving the existing PostgreSQL database structure and data.

The target is:

- keep the same business logic
- keep the same core data model
- keep the same page flow where practical
- keep the visual direction and UX structure close to the Next.js version
- remove the Node.js and Next.js deployment dependency

## Included in this transfer package

- `SYSTEM_OVERVIEW.md`
- `full_backup_with_data.sql`

`full_backup_with_data.sql` is the complete PostgreSQL dump currently available in the project. It includes schema objects and data.

## Current PHP rebuild location

The PHP rebuild currently lives in:

- `php-app/`

Main files:

- `php-app/public/index.php`
- `php-app/public/.htaccess`
- `php-app/app/bootstrap.php`
- `php-app/app/Core/*`
- `php-app/app/Controllers/*`
- `php-app/app/Repositories/*`
- `php-app/app/Views/*`
- `php-app/config/app.php`
- `php-app/.env`

## What has already been rebuilt

The following pages and flows already exist in the PHP rebuild:

- Home page
- Login
- Signup
- Dashboard
- Assessments list
- Create assessment
- Assessment summary
- Question loading for an assessment
- Answer saving into the existing `answers` table
- Products listing
- Admin overview
- Partner leads overview
- Profile page

## Route mapping

Current PHP routes:

- `/`
- `/login`
- `/signup`
- `/logout`
- `/dashboard`
- `/assessments`
- `/assessments/create`
- `/assessments/show?id=...`
- `/assessments/answers`
- `/products`
- `/admin`
- `/partner`
- `/profile`

## Design direction

The design and logic should stay similar to the existing Next.js application.

That means:

- preserve the same main information architecture
- preserve the same page naming and user flows where possible
- keep admin, user, partner, assessment, and product areas conceptually the same
- keep the same database-driven content model
- avoid introducing a completely different UX unless there is a strong reason

The current PHP layout already uses a custom visual system instead of raw default browser styling, but it is still a base layer, not the final front-end rebuild.

## Logic parity with the Next.js app

The PHP version should mirror these major functional areas from the Next.js system:

- authentication and sessions
- user dashboard
- ISO standards, clauses, and questions
- assessments and answers
- compliance scoring and results
- products and orders
- certification requests and leads
- partner lead management
- admin management screens
- profile and account pages
- uploads and supporting media
- terms, settings, and public site content

## Existing Next.js areas to mirror

The original app contains these feature groups:

- public pages
- auth pages
- user dashboard pages
- assessments and results pages
- admin pages
- partner pages
- product marketplace pages
- checkout pages
- API routes

The PHP rebuild should follow the same mental model even if implementation details differ.

## Database strategy

The database should remain the source of truth.

Use the existing PostgreSQL structure rather than redesigning it unless a specific defect requires a migration.

Primary existing tables include:

- `users`
- `accounts`
- `sessions`
- `verification_tokens`
- `iso_standards`
- `clauses`
- `questions`
- `assessments`
- `answers`
- `products`
- `orders`
- `order_items`
- `leads`
- `categories`
- `certification_requests`
- additional content and settings tables included in the backup

The PHP code should continue to read and write against those tables directly.

## Authentication approach in PHP

The PHP rebuild currently uses:

- PHP sessions
- database-backed user lookup
- password hash verification against the existing `users.password` values

OAuth and NextAuth-specific tables can remain in the database for compatibility and future migration work, but the PHP app does not need to depend on NextAuth.

## Assessment flow target

The PHP rebuild should follow the same logical assessment flow:

1. user logs in
2. user creates or opens an assessment
3. app loads the assessment standard
4. app loads related clauses and questions
5. user submits answers
6. answers are saved to `answers`
7. score and progress are calculated
8. results/report pages are shown

The current PHP rebuild already covers steps 1 to 6 in a basic form and computes a simple score update.

## Front-end expectations

The final PHP version should not look like a generic admin template unless that matches the original experience.

It should:

- remain recognizable as the same product
- preserve similar navigation structure
- preserve the same feature grouping
- preserve public-facing presentation for assessment and product areas
- preserve separate user/admin/partner areas

The existing PHP front-end base is in:

- `php-app/app/Views/layouts/app.php`

The page templates are in:

- `php-app/app/Views/`

## What is not finished yet

The following areas still need to be rebuilt fully:

- full assessment answering workflow polish
- results and reporting pages
- richer scoring logic
- password reset and email flows
- full admin CRUD
- checkout and payment flows
- file uploads
- PDF/report generation
- settings management
- terms and legal pages
- public content management

## Deployment goal

The PHP app is intended for shared hosting environments that support:

- PHP
- `.htaccess`
- PostgreSQL connection to Supabase or another external database

The preferred deployment model is:

- set the document root to `php-app/public`
- keep the rest of the application outside the public web root if possible
- configure `.env` with the live database connection

## Supabase note

If the live database remains on Supabase, the PHP database connector should support the live `DATABASE_URL`, including SSL requirements if needed by the host environment.

## Recommended next work order

1. finalize Supabase-compatible connection handling in PHP
2. test login and dashboard against the real live database
3. complete the assessment question and result flow
4. rebuild admin CRUD screens
5. rebuild product checkout and supporting flows
6. finish uploads, settings, and reports
7. perform front-end refinement to more closely match the original Next.js presentation

## File to use for database migration or restoration

Use:

- `full_backup_with_data.sql`

This is the file copied from:

- `sql/full_backup_with_data.sql`

If this system is moved to a new directory or new server, move this transfer package together with the `php-app/` application code.
