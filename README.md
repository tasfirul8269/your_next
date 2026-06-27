# Your Next Outfit

**Your Next Outfit** is a modern online clothing storefront — a Laravel 12 e‑commerce
application with a customer storefront and a full admin panel.

Designed and built by **[Frooxi](https://frooxi.com)**.

---

## Highlights

- **Storefront + Admin** — product catalog, configurable products (size/variant options),
  categories, cart, checkout, orders, customers, shipping methods, flash sales, and a
  hero/storefront content manager.
- **Built for Bangladesh** — phone‑number + OTP customer login, **SSLCommerz** & **bKash**
  payment gateways, **BDT (৳)** currency, and the `Asia/Dhaka` timezone.
- **Media on Cloudinary** — product images and videos are stored and served from Cloudinary.
- **Modular architecture** — all functionality lives in 16 self‑contained packages under
  `packages/Frooxi/`.
- **Docker‑first** — one‑command dev and production stacks (see below).

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.3+, Laravel 12 |
| Frontend | Vue 3, Alpine.js, Tailwind CSS 3, Vite 5 |
| Database | MySQL 8 |
| Media | Cloudinary |
| Payments | SSLCommerz, bKash, Cash on Delivery |

## Project layout

```
app/                     Thin Laravel shell
packages/Frooxi/         The 16 feature packages (where all the code lives):
  Admin  Attribute  Category  Checkout  Core  Customer  DataGrid
  Installer  Inventory  Payment  Product  Sales  Shipping  Shop  Theme  User
docker/                  Dev & production Docker images
docs/                    Detailed documentation (start at docs/README.md)
```

## Quick start (Docker)

> Requires Docker. Copy `.env.example` to `.env` and fill in your database, Cloudinary,
> and payment credentials first.

**Development** (live code reload, Mailpit, Adminer):
```bash
docker compose -f docker-compose.dev.yml up -d --build
docker compose -f docker-compose.dev.yml exec app php artisan migrate --force
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class="Frooxi\Installer\Database\Seeders\DatabaseSeeder" --force
docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class="Database\Seeders\SizeOptionsSeeder" --force
```
Storefront → http://localhost:8000 · Admin → http://localhost:8000/admin

**Production** (Nginx + PHP‑FPM + MySQL; reverse‑proxy friendly):
```bash
APP_PORT=8600 docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --class="Frooxi\Installer\Database\Seeders\DatabaseSeeder" --force
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --class="Database\Seeders\SizeOptionsSeeder" --force
```

Default admin login after seeding: **admin@example.com / admin123** — change this immediately.

See **[docs/SETUP_AND_DEPLOYMENT.md](docs/SETUP_AND_DEPLOYMENT.md)** for full setup, the
non‑Docker path, and deployment details.

## Documentation

- [docs/README.md](docs/README.md) — overview & architecture
- [docs/PACKAGES.md](docs/PACKAGES.md) — what each package does
- [docs/SETUP_AND_DEPLOYMENT.md](docs/SETUP_AND_DEPLOYMENT.md) — setup & deployment

## License

Proprietary — © Frooxi. All rights reserved.
