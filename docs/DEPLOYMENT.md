# Docker & Deployment

This project ships two Docker "flavors" and a GitHub Actions pipeline that auto-deploys to a VPS on push to `main`.

- **Development flavor** — `docker-compose.dev.yml` + `docker/dev/Dockerfile`. Source is bind-mounted; Vite runs with hot-reload; mail is caught locally.
- **Production flavor** — `docker-compose.prod.yml` + `docker/prod/Dockerfile`. A lean, multi-stage image with code + production deps + pre-built assets baked in, served by Nginx. Reverse-proxy friendly (no hardcoded 80/443).

> Heads-up: this environment had no PHP/Docker available when these files were written, so they were **authored and statically reviewed but not built/run here**. Do a real `docker compose ... up --build` on a machine with Docker before trusting a first production deploy.

---

## Development

```bash
cp .env.example .env
# In .env set: DB_HOST=mysql, DB_DATABASE=nextoutfit, DB_USERNAME=nextoutfit,
#              DB_PASSWORD=secret, MAIL_HOST=mailpit, MAIL_PORT=1025
docker compose -f docker-compose.dev.yml up --build
```

Then, the first time, install the app inside the running app container:

```bash
docker compose -f docker-compose.dev.yml exec app php artisan nextoutfit:install
# or, non-interactively, run migrate + seed yourself
```

| Service | URL | Purpose |
|---|---|---|
| app | http://localhost:8000 | The Laravel app (`php artisan serve`) |
| vite-admin | http://localhost:5173 | Admin Vite HMR dev server |
| vite-shop | http://localhost:5174 | Shop Vite HMR dev server |
| mailpit | http://localhost:8025 | Catches all outgoing email |
| adminer | http://localhost:8081 | DB browser (server `mysql`, user/pass from `.env`) |

Xdebug is installed but off; flip `xdebug.mode = debug` in `docker/dev/php.ini` and restart the app container to step-debug (IDE on port 9003).

---

## Production (manual)

```bash
# On the server, with a real production .env present (see below):
APP_PORT=8080 docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec -T app php artisan migrate --force
docker compose -f docker-compose.prod.yml exec -T app php artisan optimize
```

### Ports & reverse proxy
The `nginx` service binds to `${APP_PORT:-8080}` on the host — **not** 80/443 — so it won't collide with the other projects on the VPS.

- **VPS already has a reverse proxy** (Nginx/Traefik/Caddy in front of everything): leave `APP_PORT=8080` (or any free port), and point a vhost / router at `127.0.0.1:8080`. Terminate TLS at the proxy.
- **This is the only app on the box**: set `APP_PORT=80` in `.env` and let it serve directly (add your own TLS, e.g. a Caddy/Certbot sidecar).

### Database
The compose file includes a `mysql` container (data in the `dbdata` volume). If the VPS already centralizes MySQL for its other projects, remove the `mysql` service (and the app's `depends_on`) and set `DB_HOST` in `.env` to that external database instead.

### The production `.env`
The image does **not** contain a `.env` (it's `.dockerignore`d). Create `/var/www/html/.env` (or wherever `VPS_PROJECT_PATH` points) **on the server once**, and it's mounted read-only into the container. It persists across deploys because it lives on the host, not in the image. Set at minimum: `APP_KEY`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`, the `DB_*` values, the `DB_ROOT_PASSWORD` (used by the mysql container), `APP_PORT`, the Cloudinary keys, the SSLCommerz / bKash / SSLWireless credentials, and mail settings.

There is **no Redis/queue worker** in the prod stack because the app uses file cache + sync queue by default. If you switch `QUEUE_CONNECTION` to `database`/`redis`, add a worker container running `php artisan queue:work` (and a Redis service if needed).

---

## CI/CD — auto-deploy on push to `main`

`.github/workflows/deploy.yml` does, on every push to `main`:

1. **build** — builds `docker/prod/Dockerfile`, tags it `ghcr.io/<owner>/<repo>:latest` and `:<git-sha>`, and pushes to GitHub Container Registry (GHCR) using the built-in `GITHUB_TOKEN` (no extra registry secret).
2. **deploy** — SSHes into the VPS and runs: `docker compose -f docker-compose.prod.yml pull app` → `up -d` → `php artisan migrate --force` → `optimize:clear` → `optimize`.

### One-time VPS setup (before the pipeline can work)
1. Install Docker + the Compose plugin on the VPS.
2. Clone this repo once to `VPS_PROJECT_PATH` (the pipeline only needs `docker-compose.prod.yml`, `docker/prod/nginx.conf`, and your `.env` to be present there — it pulls the app image rather than building on the box).
3. Create the production `.env` there (see above).
4. Ensure the SSH user can run `docker` (in the `docker` group) and can `docker login ghcr.io` (the pipeline logs in for you using `GITHUB_TOKEN`).

### GitHub repository secrets to add
> The new owner adds these once they have repo ownership — they're intentionally left as placeholders.

| Secret | Meaning |
|---|---|
| `VPS_HOST` | VPS hostname or IP |
| `VPS_PORT` | SSH port (usually `22`) |
| `VPS_USERNAME` | SSH user (must be able to run Docker) |
| `VPS_SSH_KEY` | That user's private SSH key (PEM) |
| `VPS_PROJECT_PATH` | Absolute path to the repo checkout on the VPS |

`GITHUB_TOKEN` is provided automatically by Actions — used both to push to GHCR and to log the VPS into GHCR for the pull.

### Notes
- The image registry path defaults to `ghcr.io/nextoutfit/nextoutfit` in `docker-compose.prod.yml`; the pipeline overrides it to the actual `ghcr.io/<owner>/<repo>` (lowercased) and pins the exact `:<sha>` via the `IMAGE` env var, so each deploy runs the image it just built.
- Migrations run as an explicit pipeline step (not in the container entrypoint) so a bad migration surfaces as a failed deploy instead of a crash-looping container.
- First deploy: the GHCR package may be private by default — either keep the VPS logged in (the pipeline does this) or mark the package public.
