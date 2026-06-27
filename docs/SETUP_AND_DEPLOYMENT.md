# Setup & Deployment

## Prerequisites

- PHP 8.3+ with extensions: `calendar`, `curl`, `intl`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`
- Composer 2
- Node.js (for Vite builds — version not pinned in any config; use an LTS that supports Vite 5)
- MySQL (the installer's DB wizard only supports `mysql` as a connection type)
- A Cloudinary account (default file storage — see below) **or** reconfigure `FILESYSTEM_DISK` to `public`/`local` if you don't want to use Cloudinary

> There is no `docker-compose.yml`/Sail config in this repo despite CLAUDE.md referencing one — you're setting up PHP/MySQL/Node yourself or writing your own Docker setup.

## First-time install

```bash
composer install
cp .env.example .env   # the installer does this for you too if you skip this step
php artisan nextoutfit:install
```

The interactive installer (`packages/Frooxi/Installer/src/Console/Commands/Installer.php`) will:
1. Ask for app name/URL/timezone/locale/currency (pass `--skip-env-check` to skip and use `.env` as-is).
2. Ask for DB credentials and test the connection.
3. Generate `APP_KEY`.
4. **Run `db:wipe` then `migrate:fresh`** — this is destructive, don't run it against a database with data you want to keep.
5. Seed core reference data (locales, currencies, default attribute family, customer groups, etc.).
6. `storage:link`.
7. Prompt for an admin account (name/email/password ≥6 chars) unless `--skip-admin-creation`; optionally seeds sample products and rebuilds the index.
8. `optimize:clear`.

After install, build frontend assets for the package(s) you're working on:

```bash
cd packages/Frooxi/Admin && npm install && npm run dev    # or npm run build
cd packages/Frooxi/Shop  && npm install && npm run dev    # or npm run build
```

Then `php artisan serve` and visit `http://localhost:8000` (Shop) / `http://localhost:8000/admin` (Admin, or whatever `APP_ADMIN_URL` is set to).

## Key environment variables (`.env`)

| Variable | Default in `.env.example` | Notes |
|---|---|---|
| `APP_ADMIN_URL` | `admin` | Admin panel path prefix |
| `APP_TIMEZONE` | `Asia/Dhaka` | Confirms Bangladesh as the primary market |
| `APP_CURRENCY` | `USD` | Default store currency — but gateways (SSLCommerz, bKash) are BDT-oriented; verify currency/gateway compatibility before going live |
| `FILESYSTEM_DISK` | `cloudinary` | Set to `public` for local disk storage instead |
| `CLOUDINARY_*` | placeholders | Required if `FILESYSTEM_DISK=cloudinary` |
| `SESSION_DRIVER` | `database` | Requires the `sessions` table (migration exists) |
| `QUEUE_CONNECTION` | `sync` | **No queue worker needed today** since jobs run inline — if you later switch to `database`/`redis`, remember to run `php artisan queue:work` |
| `CACHE_STORE` | `file` | Redis client (`predis/predis`) is installed but not the active cache store by default |
| `MAIL_MAILER` | `frooxi-dynamic-smtp` | Custom mailer — per-channel SMTP settings pulled from DB config, not just `.env` |
| `SSLCZ_STORE_ID` / `SSLCZ_STORE_PASSWORD` / `SSLCZ_TESTMODE` | not in `.env.example`, read by `config/sslcommerz.php` | **Admin-panel-configured values override these at runtime** via `getConfigData()` — don't assume `.env` alone controls SSLCommerz behavior |

`config/sslwireless.php` also exists — confirm whether SSLWireless (a different Bangladeshi SMS/payment provider) is actually wired into the Payment package's method list or whether it's leftover/unused; it wasn't found referenced in `payment-methods.php` during this audit.

## Database

- Run `php artisan migrate` (without `--fresh`/`db:wipe`) for incremental updates after the initial install.
- `php artisan db:seed` for seeders (Core/Attribute/Category/Customer/Inventory/Shop/User — check `database/seeders/` for the full list and whether sample-product seeding is separate).
- After bulk product/price/inventory changes, run `php artisan indexer:index --mode=full` to rebuild `product_flat`/price/inventory index tables (the `elastic` index type will error — no Elasticsearch is configured, see [README.md](README.md)).

## Translations

```bash
php artisan nextoutfit:translations:check          # validate key parity
```

The product is **English-only** — only `lang/en` (and each package's `Resources/lang/en`) ships, and the installer's locale picker now offers English only. If you ever re-add locales, every translation key added anywhere needs a matching entry in each locale file, or the checker (and any UI relying on it) will flag gaps.

## Code style

```bash
vendor/bin/pint           # fix
vendor/bin/pint --test    # check only
```

## Testing

Package-level Pest suites exist under `packages/Frooxi/*/tests/`, but the suite is **not runnable as-is**: there is no root `phpunit.xml` wiring the suites together, and several test files still reference removed packages (Tax/CartRule/CatalogRule/Marketing). So `vendor/bin/pest` will not run cleanly yet. Wiring up `phpunit.xml` and getting a checkout/payment/admin-login smoke suite green should be an early priority — see the "known follow-ups" in [README.md](README.md).

## Deployment

Docker (dev + prod flavors) and a GitHub Actions auto-deploy pipeline now exist — see **[DEPLOYMENT.md](DEPLOYMENT.md)** for the full story (compose files, reverse-proxy guidance, VPS setup, CI secrets). Other operational notes:

- `APP_DEBUG_ALLOWED_IPS` (read in `app/Providers/AppServiceProvider.php`) enables/disables **Laravel Debugbar** based on the requesting IP — it lets you turn on Debugbar for your own IP in an environment where `APP_DEBUG` is otherwise off. It's not a general security/access gate, just a Debugbar toggle.
- `storage/installed` is written by the Installer (`storage_path('installed')`, dispatches a `frooxi.installed` event) as a marker that setup completed. If you're scripting deployments, make sure this file persists across deploys.
- Cloudinary is the default file store — any deployment automation should not assume local disk uploads persist across deploys.
- **`app/Providers/AppServiceProvider.php` registers an Eloquent `morphMap`** translating legacy `Webkul\Product\Models\Product`, `Webkul\Product\Models\ProductFlat`, and `Webkul\BookingProduct\Models\BookingProduct` polymorphic type strings to their `Frooxi` equivalents. This is a deliberate compatibility bridge for old polymorphic rows (in `order_items`/`cart_items`) that predate the Webkul→Frooxi rename and haven't been migrated by the `fix_webkul_morph_types_to_frooxi` migrations. **Do not remove this morph map** until you've confirmed (via a DB query for any remaining `Webkul\*` strings in morphable columns) that no legacy rows remain — removing it early will break polymorphic lookups for any unmigrated historical record. Note `Frooxi\BookingProduct` itself doesn't exist as a package in this fork (Bagisto's booking-product feature was dropped); the reference is guarded with `class_exists()` so it's safe.
