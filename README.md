# PHP System Ready

This directory is the move-ready package for the shared-hosting PHP rebuild.

Contents:

- `app/` the PHP application
- `transfer/` system handover notes and the database backup

Move this directory to the new location, then:

1. point the web root to `app/public`
2. update `app/.env`
3. connect the app to the live Supabase/PostgreSQL database
4. test login, dashboard, assessments, products, and admin CRUD
