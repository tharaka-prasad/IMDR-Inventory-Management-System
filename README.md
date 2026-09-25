# IMDR Inventory & Asset Management System

Laravel 12 + React 19 + Inertia.js + MySQL + Tailwind CSS. Roles: **Super Admin**, **Admin**, **Assignee**.

This archive contains the **application code only** (models, migrations, controllers, policies,
services, routes, seeders, and every React/Inertia page and component). It does not include the
generic Laravel skeleton files (`artisan`, `public/index.php`, `config/*.php`, `storage/`
directories, etc.) or the `vendor/`/`node_modules/` folders, because generating those requires
running Composer and npm against the internet, which this build environment cannot do.

## Setup (5 steps)

1. **Create a fresh Laravel 12 skeleton** (this brings in `artisan`, `public/index.php`, the
   default `config/*.php` files, `storage/`, etc.):
   ```bash
   composer create-project laravel/laravel:^12.0 imdr-inventory
   cd imdr-inventory
   ```

2. **Copy this archive's contents into the new project**, overwriting where files already exist:
   ```bash
   # from inside the extracted archive folder
   cp -r app/* /path/to/imdr-inventory/app/
   cp -r database/* /path/to/imdr-inventory/database/
   cp -r resources/js /path/to/imdr-inventory/resources/
   cp -r resources/views/* /path/to/imdr-inventory/resources/views/
   cp routes/web.php /path/to/imdr-inventory/routes/web.php
   cp bootstrap/app.php /path/to/imdr-inventory/bootstrap/app.php
   cp bootstrap/providers.php /path/to/imdr-inventory/bootstrap/providers.php
   cp composer.json /path/to/imdr-inventory/composer.json
   cp package.json /path/to/imdr-inventory/package.json
   cp vite.config.js tailwind.config.js .env.example /path/to/imdr-inventory/
   ```

3. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

4. **Configure environment and database**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   # edit .env: set DB_DATABASE, DB_USERNAME, DB_PASSWORD for a MySQL database you've created
   php artisan storage:link
   ```

5. **Migrate, seed, and run**:
   ```bash
   php artisan migrate --seed
   npm run build   # or `npm run dev` in a second terminal while developing
   php artisan serve
   ```

## Default credentials (from the seeders)

| Role        | Email                  | Password  |
|-------------|------------------------|-----------|
| Super Admin | superadmin@imdr.lk     | password  |
| Admin       | admin@imdr.lk          | password  |
| Assignee    | assignee@imdr.lk       | password  |

**Change these immediately in any non-local environment.**

## What's implemented

- **RBAC**: 3 roles enforced via route middleware (`role:super_admin,admin,...`) *and* Policies
  (`app/Policies`), backed by Spatie `laravel-permission` roles/permissions for finer-grained
  checks. Assignees are scoped to only the assets currently/previously assigned to them at the
  query level (`InventoryController::index`), not just hidden in the UI.
- **Asset register** (`inventories` table) covers Fixed Assets, IT Assets, and Consumables via
  an `asset_type` column rather than three separate tables, since they share ~90% of their
  fields — filterable per-module from the Inventory screens.
- **Asset codes** (`IMDR-IT-001` style) and **unique QR codes + PNG images** are generated
  automatically on create (`AssetCodeService`, `QrCodeService`).
- **Stock movements** (issue / return / transfer / add) are centralized in `StockService`,
  wrapped in DB transactions with row locking, and each one writes a `stock_histories` ledger
  row and an `audit_logs` entry. Assignment history is never hard-deleted (soft deletes +
  append-only `returns`/`transfers` tables).
- **Depreciation**: SLM and WDV methods, net book value recalculated automatically
  (`Depreciation::recalculate()`), triggered from `DepreciationService` on create/update.
- **Audit log**: every create/update/soft-delete on core models (via the `Auditable` trait) plus
  explicit entries for issue/return/transfer/maintenance actions and login/logout/failed-login
  (`LogAuthenticationEvents` listener).
- **Reports**: 8 report types, each with PDF (DomPDF) and Excel (Laravel Excel) export and
  date-range filtering (`ReportController`).
- **Frontend**: reusable `DataTable`, `SearchFilter`, `Modal`, `ConfirmDelete`, `StatCard`,
  `QRCard`, `FormInput`, `SelectInput`, `Pagination` components; a role-aware `Sidebar` that only
  shows **Settings** to Super Admins; Recharts on the Dashboard.

## Known gaps to review before production use

- `StoreAdminRequest`/`StoreUserRequest`/etc. cover the documented fields — re-check them
  against any additional validation rules your organization requires (password complexity,
  phone format, etc.).
- No automated tests are included; add PHPUnit/Pest feature tests for the stock-movement edge
  cases (partial returns, transfers, insufficient stock) before going live.
- Email-based password reset requires mail configuration in `.env` (`MAIL_*`); the "Reset
  Password" *button* in User Management instead generates and displays a temporary password
  directly, since no mail server is assumed.
- A scheduled command to periodically call `Depreciation::recalculate()` for every asset (e.g.
  monthly, via `routes/console.php`) is recommended so Net Book Value stays current even for
  assets nobody edits.
