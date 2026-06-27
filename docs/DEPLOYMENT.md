# Deployment

The repository includes a production Docker Compose stack and a GitHub Actions workflow for VPS deployment.

## Production stack

Production Compose file:

```text
docker-compose.prod.yml
```

Services:

| Service | Purpose |
| --- | --- |
| `app` | PHP-FPM runtime with Composer dependencies and built assets |
| `nginx` | Serves `public/` and proxies PHP requests to `app:9000` |
| `mysql` | MySQL 8 database |

Persistent volumes:

| Volume | Purpose |
| --- | --- |
| `dbdata` | MySQL data |
| `public_assets` | Shared public assets for Nginx |
| `storage_data` | Laravel storage |

The Nginx container binds to `${APP_PORT:-8080}` on the host. Put a host-level reverse proxy in front of it for HTTPS.

## Production environment

Create a real `.env` on the server. Do not bake secrets into the image.

Required production values include:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
APP_ADMIN_URL=admin

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
DB_ROOT_PASSWORD=your_root_password

APP_PORT=8080
QUEUE_CONNECTION=sync
CACHE_STORE=file
SESSION_DRIVER=database
```

Set storage and payment credentials for the environment:

```dotenv
FILESYSTEM_DISK=cloudinary
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_URL=cloudinary://your_api_key:your_api_secret@your_cloud_name
```

Add SSLCommerz and bKash values based on the payment controller and config files used by the project.

## Manual production deploy

From the project directory on the server:

```bash
docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec app php artisan optimize
docker compose -f docker-compose.prod.yml exec app php artisan indexer:index --type=price --type=inventory --type=flat --mode=full
```

Run seeders only when you intentionally need base data:

```bash
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --class="Frooxi\\Installer\\Database\\Seeders\\DatabaseSeeder" --force
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --class="Database\\Seeders\\SizeOptionsSeeder" --force
```

Do not run `nextoutfit:install` on a production database with real data. It wipes and rebuilds the schema.

## GitHub Actions deploy workflow

Workflow file:

```text
.github/workflows/deploy.yml
```

The workflow:

1. Runs on pushes to `main` or manual dispatch.
2. Builds the production image with `docker/prod/Dockerfile`.
3. Pushes the image to GitHub Container Registry.
4. SSHes into the VPS.
5. Runs Docker Compose.
6. Runs migrations and Laravel optimize commands.

Required repository secrets:

| Secret | Purpose |
| --- | --- |
| `VPS_HOST` | VPS hostname or IP |
| `VPS_PORT` | SSH port |
| `VPS_USERNAME` | SSH user |
| `VPS_SSH_KEY` | Private SSH key for the deploy user |
| `VPS_PROJECT_PATH` | Absolute path to the project on the VPS |

The workflow uses GitHub's `GITHUB_TOKEN` for GHCR authentication.

## Reverse proxy notes

Terminate HTTPS at the host reverse proxy. Forward requests to:

```text
127.0.0.1:${APP_PORT}
```

Make sure the proxy preserves:

- `Host`
- `X-Forwarded-For`
- `X-Forwarded-Proto`

The app trusts proxies in `bootstrap/app.php`.

## Post-deploy checks

After each deploy, check:

```bash
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs --tail=100 app
docker compose -f docker-compose.prod.yml logs --tail=100 nginx
```

Then verify:

- Storefront home page loads.
- Admin login page loads.
- Product listing returns products.
- Cart add works.
- Checkout reaches payment selection.
- SSLCommerz and bKash callback URLs match gateway dashboard settings.
- Uploaded media resolves from the configured disk.
