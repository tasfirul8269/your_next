# Setup and deployment

This file covers local setup and the commands a new developer needs before working on the project. Production deployment details are in [DEPLOYMENT.md](DEPLOYMENT.md).

## Requirements

For Docker setup:

- Docker Desktop or Docker Engine with Compose.

For non-Docker setup:

- PHP 8.3 with `calendar`, `curl`, `intl`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, and `tokenizer`.
- Composer 2.
- Node.js 20.
- MySQL 8.

## Environment file

Create `.env` from the example:

```bash
cp .env.example .env
```

Important variables:

| Variable | Purpose |
| --- | --- |
| `APP_NAME` | Application name shown by Laravel |
| `APP_URL` | Public app URL |
| `APP_ADMIN_URL` | Admin path segment. Default is `admin` |
| `APP_TIMEZONE` | Default timezone. The example uses `Asia/Dhaka` |
| `APP_CURRENCY` | Default currency. The example uses `BDT` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | MySQL connection |
| `DB_ROOT_PASSWORD` | MySQL root password used by Docker Compose |
| `FILESYSTEM_DISK` | Active storage disk. `public` and `cloudinary` are configured |
| `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`, `CLOUDINARY_URL` | Cloudinary storage settings |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT` | Mail settings |
| `QUEUE_CONNECTION` | Queue driver. The example uses `sync` |
| `CACHE_STORE` | Cache driver. The example uses `file` |

The example file currently contains `FILESYSTEM_DISK` twice. Laravel will use the later value when the environment is parsed. Keep only the intended value in a real environment file.

## Docker development setup

Set the development database and mail values:

```dotenv
DB_HOST=mysql
DB_DATABASE=nextoutfit
DB_USERNAME=nextoutfit
DB_PASSWORD=secret
DB_ROOT_PASSWORD=rootsecret
MAIL_HOST=mailpit
MAIL_PORT=1025
```

Start the stack:

```bash
docker compose -f docker-compose.dev.yml up --build
```

Services:

| Service | URL or port |
| --- | --- |
| App | `http://localhost:8000` |
| Admin | `http://localhost:8000/admin` |
| Admin Vite | `5173` |
| Shop Vite | `5174` |
| MySQL | `${DB_FORWARD_PORT:-3306}` |
| Mailpit | `http://localhost:8025` |
| Adminer | `http://localhost:8081` |

Initialize the app:

```bash
docker compose -f docker-compose.dev.yml exec app php artisan key:generate
docker compose -f docker-compose.dev.yml exec app php artisan migrate --force
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class="Frooxi\\Installer\\Database\\Seeders\\DatabaseSeeder" --force
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class="Database\\Seeders\\SizeOptionsSeeder" --force
docker compose -f docker-compose.dev.yml exec app php artisan indexer:index --type=price --type=inventory --type=flat --mode=full
```

If you want sample clothing data, review the root seeders in `database/seeders/` before running them.

## Non-Docker setup

Install PHP dependencies:

```bash
composer install
php artisan key:generate
```

Install frontend dependencies for the active builds:

```bash
cd packages/Frooxi/Admin && npm install
cd ../Shop && npm install
cd ../Installer && npm install
```

Run migrations and seeders from the repository root:

```bash
php artisan migrate --force
php artisan db:seed --class="Frooxi\\Installer\\Database\\Seeders\\DatabaseSeeder" --force
php artisan db:seed --class="Database\\Seeders\\SizeOptionsSeeder" --force
php artisan indexer:index --type=price --type=inventory --type=flat --mode=full
```

Run the app:

```bash
php artisan serve
```

Run Vite in separate terminals:

```bash
cd packages/Frooxi/Admin && npm run dev
cd packages/Frooxi/Shop && npm run dev
```

Use the Installer Vite build only when working on the installer UI.

## Installer command

The installer command is:

```bash
php artisan nextoutfit:install
```

Use it for a fresh install only. The command calls `db:wipe` and `migrate:fresh`, then seeds base data and creates or updates admin credentials.

Useful options:

| Option | Purpose |
| --- | --- |
| `--skip-env-check` | Use the existing `.env` without prompting |
| `--skip-admin-creation` | Skip interactive admin creation |
| `--skip-github-star` | Skip the prompt at the end |

## Build assets

Build Admin and Shop assets:

```bash
cd packages/Frooxi/Admin && npm run build
cd ../Shop && npm run build
```

The production Dockerfile builds Admin and Shop assets during the image build.

## Routine commands

```bash
php artisan optimize:clear
php artisan optimize
php artisan storage:link
php artisan indexer:index --type=price --type=inventory --type=flat --mode=full
php artisan nextoutfit:translations:check
```

## Testing status

Pest and Playwright test files exist under package test folders. This repository does not include a root `phpunit.xml` in the current workspace, and several tests still reference inactive features.

Before using tests as a release gate:

1. Add a root `phpunit.xml`.
2. Remove or update tests for inactive features.
3. Add a small smoke suite for product listing, cart, checkout, Cash on Delivery, SSLCommerz callback handling, and bKash callback handling.
