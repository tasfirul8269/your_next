# CLAUDE.md

Guidance for Claude Code (and any developer) working in this repository. **This file is kept accurate to what is actually on disk** — if you change the project's structure, update this file. For deeper detail see the [`docs/`](docs/) directory (start with [docs/README.md](docs/README.md)).

## Project Overview

"Your Next Outfit" — a Laravel 12 e-commerce storefront for an online clothing shop, built by **Frooxi** (frooxi.com). PHP 8.3+, Vue 3 + Alpine.js, Tailwind CSS 3, Vite 5, MySQL.

- **`Frooxi`** is the internal PHP namespace / the dev company. **`Next Outfit` / `nextoutfit`** is the client storefront brand (used in user-facing strings and artisan command names).
- Target market is Bangladesh: phone-number + OTP customer auth, SSLCommerz & bKash payment gateways, `Asia/Dhaka` timezone.
- **16 packages** under `packages/Frooxi/`: Admin, Attribute, Category, Checkout, Core, Customer, DataGrid, Installer, Inventory, Payment, Product, Sales, Shipping, Shop, Theme, User. (Other optional e-commerce modules — Tax, CartRule, CMS, Marketing, RMA, GDPR, Paypal/Stripe/Razorpay/PayU, etc. — are **not** part of this project. Don't reference them.)

## Common Commands

### Development
```bash
composer install                  # Install PHP dependencies
php artisan nextoutfit:install    # Full install (env wizard, migrate:fresh, seed, admin user)
php artisan serve                 # Start PHP dev server
php artisan optimize:clear        # Clear all caches (run after config/code changes)
php artisan nextoutfit:version    # Print installed version
```

Frontend builds are **per-package** (Admin, Shop, Installer each have their own Vite build):
```bash
cd packages/Frooxi/Admin && npm install && npm run dev    # or: npm run build
cd packages/Frooxi/Shop  && npm install && npm run dev    # or: npm run build
```
- Admin builds to `public/themes/admin/default/build/`, Shop to `public/themes/shop/default/build/`.
- The Vite registry lives in `config/nextoutfit-vite.php` (consumed by `Frooxi\Theme\Themes`).

### Docker (added June 2026)
```bash
docker compose -f docker-compose.dev.yml up --build     # dev: app + Vite HMR + MySQL + Mailpit + Adminer
docker compose -f docker-compose.prod.yml up -d         # prod: app + Nginx + MySQL
```
See [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) for the full dev/prod story and the CI/CD deploy pipeline.

### Code Style
```bash
vendor/bin/pint           # Fix PHP code style (Laravel Pint, preset: laravel)
vendor/bin/pint --test    # Check only (use this before finishing any PHP change)
```

### Translations (English-only)
This product ships **English only** (`lang/en` + each package's `Resources/lang/en`). When adding translation keys, add them to the `en` files. Consistency check:
```bash
php artisan nextoutfit:translations:check
```

### Tests — ⚠️ not runnable as-is
Package-level Pest suites exist under `packages/Frooxi/*/tests/`, but there is **no root `phpunit.xml`** and several test files still reference removed packages, so `vendor/bin/pest` won't run cleanly yet. Standing up a working test runner + a checkout/payment smoke suite is an open task — see [docs/README.md](docs/README.md) "known follow-ups". Do **not** claim tests pass without first wiring up the runner and confirming.

## Architecture

### Modular monolith
`app/` is a thin shell; all functionality lives in the 16 packages. Each package **dual-registers**:
1. **`bootstrap/providers.php`** — the package's main ServiceProvider (routes, views, translations, migrations, config).
2. **`config/concord.php`** — the package's ModuleServiceProvider (Konekt Concord model registration).

### Core patterns (mandatory, not optional)
- **Contract / Model / Proxy**: every entity has an interface (`Contracts/`), an Eloquent model (`Models/`), and a Concord Proxy (`Models/<Name>Proxy.php`). Reference the **Proxy** when crossing packages; repositories' `model()` returns the **Contract**.
- **Repository pattern (Prettus L5)**: all DB access goes through `Repositories/` (extend `Frooxi\Core\Eloquent\Repository`). Never query models directly in controllers.
- **Event-driven**: extend behavior via listeners, don't edit core packages.
- **Path repositories**: `composer.json` symlinks `packages/*/*`. Editing package code takes effect immediately; only run `composer dump-autoload` when adding a *new* package.

### Package anatomy
```
packages/Frooxi/<Package>/src/
├── Config/           # system.php (admin settings tree), menu.php, acl.php, carriers.php, payment-methods.php
├── Contracts/        # interfaces
├── Database/         # Migrations/, Seeders/, Factories/
├── DataGrids/        # admin list-table classes (extend Frooxi\DataGrid\DataGrid)
├── Http/Controllers/ # Admin/ and Shop/ (+ API/ for JSON endpoints)
├── Models/           # Eloquent models + Proxy classes
├── Repositories/     # Prettus L5 repositories
├── Resources/        # views/ (Blade), lang/en/, assets/ (Vite)
├── Routes/           # multiple route files per package (see below)
├── Providers/        # <Name>ServiceProvider + ModuleServiceProvider
└── Listeners/
```

### Routing
Packages load **multiple** route files with different middleware:
- **Admin**: `auth-routes.php` (public login) vs. the `admin`-guarded files (`catalog-`, `sales-`, `customers-`, `settings-`, `storefront-`, `configuration-`, `rest-`, `web-protected.php`), plus `api.php` mounted at `api/v1/admin` under the **`api` middleware only — no admin guard** (a known security gap; see [docs/api/admin.md](docs/api/admin.md)).
- **Shop**: `store-front-routes.php`, `customer-routes.php` (public auth + `customer`-guarded account), `checkout-routes.php` (cart, onepage, SSLCommerz/bKash callbacks), `api.php`.

### Auth
Two session guards (`config/auth.php`): `admin` → `Frooxi\User\Models\Admin`, `customer` → `Frooxi\Customer\Models\Customer`. Customer auth is **by phone number** with OTP (SMS via `Frooxi\Customer\Services\SslWirelessSmsService`, configured in `config/sslwireless.php`).

### Payments & shipping (active set)
- Payment methods (`packages/Frooxi/Payment/src/Config/payment-methods.php`): `cashondelivery`, `sslcommerz` (redirect + IPN), `bkash` (redirect). SSLCommerz config in `config/sslcommerz.php` (admin-panel values override `.env` at runtime).
- Shipping carriers (`packages/Frooxi/Shipping/src/Config/carriers.php`): `customshipping` only (admin-managed methods in the `shipping_methods` table).

### Adding a new package
1. Create `packages/Frooxi/<Name>/src/` with the standard structure.
2. Add the PSR-4 namespace to root `composer.json` autoload.
3. Register the ServiceProvider in `bootstrap/providers.php`.
4. Register the ModuleServiceProvider in `config/concord.php`.
5. `composer dump-autoload && php artisan optimize:clear`.

## Storage & external services
- **File storage defaults to Cloudinary** (`FILESYSTEM_DISK=cloudinary`); set to `public` for local disk. Don't assume local uploads persist across deploys.
- Cache = file, queue = sync, sessions = database by default. `predis/predis` is installed but **not** wired up.
- Polymorphic types use `Frooxi\*` class names. Two historical migrations (`fix_webkul_morph_types_to_frooxi`, `fix_remaining_webkul_morph_types`) convert any legacy type strings on migrate; they're inert on a fresh install.

## Safety rails
- Run `vendor/bin/pint --test` before finishing any PHP change.
- Don't reintroduce references to removed packages (Tax, CartRule, CMS, Marketing, RMA, GDPR, MagicAI, DataTransfer, BookingProduct, Paypal/Stripe/Razorpay/PayU). If you import one, the autoloader can't resolve it.
- Don't modify `bootstrap/providers.php` or `config/concord.php` without understanding the full provider chain.
- Don't add/remove Composer dependencies without a reason recorded in the PR.
