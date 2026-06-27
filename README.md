# Your Next Outfit

Your Next Outfit is a Laravel ecommerce application for a clothing storefront. It includes a customer shop, an admin panel, cart and checkout, simple and configurable products, customer accounts, custom shipping methods, and payment through Cash on Delivery, SSLCommerz, and bKash.

The codebase is a modular Laravel monolith. The Laravel app shell stays small, while the domain code lives in packages under `packages/Frooxi/`.

## Tech stack

| Area | Used in this project |
| --- | --- |
| Backend | PHP 8.3, Laravel 12 |
| Frontend | Blade, Vue 3, Alpine.js in the admin panel, Tailwind CSS 3, Vite 5 |
| Database | MySQL 8 |
| Auth | Session guards for `admin` and `customer` |
| Storage | Local public disk or Cloudinary, controlled by `FILESYSTEM_DISK` |
| Payments | Cash on Delivery, SSLCommerz, bKash |
| Shipping | Admin-managed custom shipping methods |
| Tests | Pest test files and Playwright files exist, but the repository does not include a root `phpunit.xml` |
| Deployment | Docker Compose for development and production, plus a GitHub Actions deploy workflow |

## Main directories

```text
app/                    Thin Laravel app layer.
bootstrap/              Laravel bootstrapping and middleware registration.
config/                 App, auth, filesystem, mail, queue, theme, and payment config.
database/               Root migrations and project seeders.
docker/                 Development and production Docker images.
docs/                   Developer handover documentation.
packages/Frooxi/        Domain packages for admin, shop, checkout, product, sales, and related modules.
public/                 Public entry point, theme assets, fonts, and compiled assets.
resources/              Root CSS and JavaScript entries.
routes/                 Root Laravel route files. Package routes are loaded by package providers.
```

## Quick start with Docker

Copy the environment file and set the database values first:

```bash
cp .env.example .env
```

For the development stack, set these values in `.env`:

```dotenv
DB_HOST=mysql
DB_DATABASE=nextoutfit
DB_USERNAME=nextoutfit
DB_PASSWORD=secret
DB_ROOT_PASSWORD=rootsecret
MAIL_HOST=mailpit
MAIL_PORT=1025
```

Start the containers:

```bash
docker compose -f docker-compose.dev.yml up --build
```

The development stack serves:

| Service | URL |
| --- | --- |
| Storefront | `http://localhost:8000` |
| Admin panel | `http://localhost:8000/admin` |
| Mailpit | `http://localhost:8025` |
| Adminer | `http://localhost:8081` |

After the app container is running, initialize the app:

```bash
docker compose -f docker-compose.dev.yml exec app php artisan key:generate
docker compose -f docker-compose.dev.yml exec app php artisan migrate --force
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class="Frooxi\\Installer\\Database\\Seeders\\DatabaseSeeder" --force
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class="Database\\Seeders\\SizeOptionsSeeder" --force
docker compose -f docker-compose.dev.yml exec app php artisan indexer:index --type=price --type=inventory --type=flat --mode=full
```

The installer command is also available:

```bash
php artisan nextoutfit:install
```

Use it only when you are ready for a fresh install. It calls `db:wipe` and `migrate:fresh`.

## Documentation

| Document | Purpose |
| --- | --- |
| [docs/SETUP_AND_DEPLOYMENT.md](docs/SETUP_AND_DEPLOYMENT.md) | Local setup, environment variables, build commands, and install notes |
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Package architecture, request flow, auth, cart flow, and frontend builds |
| [docs/PACKAGES.md](docs/PACKAGES.md) | What each package does in this codebase |
| [docs/API_REFERENCE.md](docs/API_REFERENCE.md) | Active routes and API endpoints used by the storefront and admin UI |
| [docs/api/shop.md](docs/api/shop.md) | Shop API details |
| [docs/api/admin.md](docs/api/admin.md) | Admin AJAX and API details |
| [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) | Production Docker and GitHub Actions deployment |

## Handover notes

The active product types are `simple` and `configurable`. The active shipping carrier is `customshipping`. The active payment methods are `cashondelivery`, `sslcommerz`, and `bkash`.

Some test files still reference removed or inactive features. Treat the test suite as documentation debt until a root `phpunit.xml` and a clean smoke suite are added.

## License

Proprietary. Confirm the handover terms before reusing this code outside the project.
