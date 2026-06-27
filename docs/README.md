# Your Next Outfits (codename "Yournext"/"Frooxi"/"NextOutfit") — Developer Documentation

This is the canonical, code-verified documentation for this repository. It was written by reading the actual source on **2026-06-26** and supersedes two other "documentation-like" things already in this repo — read the [Stale Docs Warning](#stale-docs-warning) below before trusting anything else.

## What is this project?

A Laravel 12 e-commerce platform for an online clothing store ("Your Next Outfits"), built as a **deep fork of [Bagisto](https://bagisto.com/)** (an open-source Laravel e-commerce platform by Webkul). The fork:

- Renamed the `Webkul` PHP namespace to `Frooxi` everywhere (composer name is `nextoutfit/nextoutfit`).
- Stripped out most of Bagisto's optional modules (no Paypal/Stripe/Razorpay/PayU, no CMS, Marketing, Tax, CartRule, RMA, GDPR, MagicAI, etc. — see [Package Reference](PACKAGES.md)).
- Added **Bangladesh-specific payment gateways**: SSLCommerz and bKash (see [API Reference](API_REFERENCE.md) and [Q&A](HANDOVER_QA.md)).
- Switched default file storage to **Cloudinary**.
- Reduced locale support down to English only (despite installer code still offering 21 languages — see gaps below).

## Documentation Index

| Doc | Contents |
|---|---|
| [ARCHITECTURE.md](ARCHITECTURE.md) | Design patterns (Concord/Proxy/Repository), package anatomy, request lifecycle |
| [PACKAGES.md](PACKAGES.md) | Every package: purpose, models, tables, repositories, config |
| [API_REFERENCE.md](API_REFERENCE.md) | Route index for Admin + Shop: method, path, controller, auth |
| [api/shop.md](api/shop.md) | **Detailed** Shop API: per-endpoint request params, validation, response shapes |
| [api/admin.md](api/admin.md) | **Detailed** Admin REST API: per-endpoint detail + the unauthenticated-API security warning |
| [SETUP_AND_DEPLOYMENT.md](SETUP_AND_DEPLOYMENT.md) | Local setup, install command, env vars |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Docker (dev/prod), CI/CD deploy pipeline, VPS setup |
| [HANDOVER_QA.md](HANDOVER_QA.md) | Anticipated questions a new developer would ask, with answers |

## Tech Stack Audit

### Backend — solid, current

| Component | Version | Notes |
|---|---|---|
| PHP | ^8.3 | Current LTS-adjacent, fine |
| Laravel | ^12.0 | Latest major, fine |
| Konekt Concord | ^1.16 | Powers the Model/Contract/Proxy module system — core to how every package works |
| Prettus L5 Repository | ^2.6 | Repository pattern wrapper, used everywhere — **package is effectively unmaintained upstream** (last meaningful release years ago), but stable enough for a 8.3/12 stack since it's just a thin Eloquent wrapper |
| Laravel Sanctum | ^4.3 | Used for API token support (admin/customer both have `api_token` columns); **not currently used for stateless SPA/mobile auth** — sessions are the real auth mechanism (see below) |
| Laravel Cashier | ^16.0 | **Installed but no Stripe/billing code anywhere in `packages/Frooxi`** — appears to be a leftover dependency from upstream Bagisto. Dead weight; safe candidate for removal after confirming nothing depends on it. |
| astrotomic/laravel-translatable | ^11.16 | Backbone of every `*Translation` table (categories, attributes, channels, etc.) |
| kalnoy/nestedset | ^6.0 | Category tree (adjacency via `_lft`/`_rgt`) |
| intervention/image | ^2.4\|^3.0 | Image resizing |
| cloudinary-labs/cloudinary-laravel | ^2.0 | **Primary** file storage (`FILESYSTEM_DISK=cloudinary` in `.env.example`) |
| maatwebsite/excel | ^3.1 | DataGrid CSV/Excel export |
| predis/predis | ^2.2 | Redis client installed, but `.env.example` defaults `CACHE_STORE=file` and `QUEUE_CONNECTION=sync` — **Redis is not actually wired up by default** |
| spatie/laravel-responsecache | ^7.4 | Full-page cache, installed but not obviously enabled (no FPC package, no cache middleware found wired into shop routes during this audit — verify before relying on it) |
| mpdf/mpdf + barryvdh/laravel-dompdf | both ^2-3 / ^8.2 | Two PDF libraries installed simultaneously — invoice PDF generation likely uses one; redundant dependency, worth consolidating |
| pragmarx/google2fa | ^8.0 | Backs Admin's two-factor auth (`/admin/two-factor/*` routes) |
| khaled.alshamaa/ar-php | ^6.0 | Arabic text helpers — orphaned given only `en` locale ships (see gaps) |

### Frontend — solid, current

| Component | Version | Notes |
|---|---|---|
| Vite | ^5.4 | Build tool, 3 independent builds (Admin, Shop, Installer) |
| Vue 3 | ^3.5 | Used inside Blade via Vite, not a full SPA |
| Tailwind CSS | ^3.3 | Both Admin and Shop |
| Alpine.js | ^3.15 | **Admin only** — used alongside Vue 3 in the same package, which is two reactivity systems coexisting. Not wrong, but a maintenance surface a new dev should know about. |
| vee-validate | ^4.9 | Form validation, both packages |
| Chart.js | ^4.5 | Admin dashboard charts |
| vuedraggable | ^4.1 | Admin drag-and-drop (e.g. attribute ordering) |

### Infrastructure — current state

| Area | Status |
|---|---|
| Docker | **Added (June 2026)** — `docker-compose.dev.yml` (dev: app + Vite HMR + MySQL + Mailpit + Adminer) and `docker-compose.prod.yml` (prod: app + Nginx + MySQL, reverse-proxy-friendly). See [DEPLOYMENT.md](DEPLOYMENT.md). Note: the *historical* CLAUDE.md/AGENTS.md described a Sail stack with Redis/Elasticsearch/Kibana that never existed in this fork — the new Docker setup reflects what the app actually uses (file cache, sync/DB queue, MySQL search). |
| CI/CD | **Added (June 2026)** — one GitHub Actions workflow (`.github/workflows/deploy.yml`) builds the prod image, pushes to GHCR, and redeploys to the VPS on push to `main`. No test-running CI yet (see tests below). |
| Elasticsearch | **Not used.** `Indexer.php` has an `elastic` index-type code path but there is no `config/elasticsearch.php` and no connection — product search runs on MySQL (`product_flat` + `product_price_indices` + `product_inventory_indices`). The `elastic` indexer mode would error if invoked; treat search as MySQL-only. |
| Tests | Package-level **Pest test suites exist** under `packages/Frooxi/*/tests/`, but there is **no root `phpunit.xml`** to run them and several files reference removed packages (see the "known follow-ups" below). So the suite is **not runnable as-is** — the biggest handover risk. A new dev should wire up `phpunit.xml` and get at least a checkout/payment smoke suite green. |
| Locales | English-only. Only `en` ships; the installer picker and translation checker are now English-only too. |
| Packages | **16 packages** in `packages/Frooxi/`: Admin, Attribute, Category, Checkout, Core, Customer, DataGrid, Installer, Inventory, Payment, Product, Sales, Shipping, Shop, Theme, User. (Upstream Bagisto's other ~25 modules — Tax, CartRule, CMS, Marketing, RMA, GDPR, Paypal/Stripe/Razorpay/PayU, etc. — are not part of this fork.) |

## June 2026 cleanup pass

This codebase started as a Bagisto fork, then went through several rounds of AI-IDE-assisted edits that left a lot of "hidden, not removed" dead code. A dedicated cleanup pass (June 2026) removed the dead weight and made the tree honest. What was done:

**Deleted (dead, confirmed unreferenced):**
- `.qoder/` (stale auto-generated wiki referencing the old `Webkul` namespace) and root `AGENTS.md` (generic, inaccurate).
- Three abandoned debug scripts at repo root (`check_groups.php`, `create_attribute.php`, `verify_attribute.php`).
- The orphaned `agent_conversations` / `agent_conversation_messages` migration (AI-tool bookkeeping tables, zero app references) and two no-op Tax-remnant migrations.
- The disabled `moneytransfer` payment method (class, config, settings page, listener branch) and the disabled `flatrate` / `free` shipping carriers (classes, config, settings pages) — only `customshipping` was ever active.
- Orphaned ACL permission entries for removed features (RMA, CMS, Tax, GDPR, DataTransfer) that pointed at routes which don't exist.
- Stub controllers (`Api/CMSController`, `Api/MarketingController`) and orphaned views (RMA email template, Tax shimmer) for removed packages.
- Commented-out dead route imports/groups (Marketing, CMS, Reporting-commented-lines, Notification, MagicAI, BookingProduct, DataTransfer, Tax) across the route files.
- Test files for entirely-removed features (the whole `Admin/tests/Feature/Marketing/` and `Cms/` dirs, Tax/DataTransfer settings tests, the CartRule coupon-limit test).

**Fixed:**
- **Latent fatal bug**: `OnepageController` and `OrderRepository` imported `Frooxi\CartRule\Exceptions\CouponUsageLimitExceededException` from the removed CartRule package — the class didn't exist, so any exception during order creation would have thrown `Class not found`. Recreated the exception as `Frooxi\Checkout\Exceptions\CouponUsageLimitExceededException` and repointed the imports.
- **Branding**: removed leftover `webkul.com` links from the admin login/forget-password footers, the installer footer, and the admin "Powered by" string — all now point to Frooxi only. (`Frooxi` = the dev company / internal namespace; `Next Outfit` = the client's storefront brand.)
- **Artisan commands** standardized to one prefix: `nextoutfit:install` (was `yournext:install`) and `nextoutfit:translations:check` (was `frooxi:translations:check`); `nextoutfit:version` was already correct.
- **Vite config** consolidated: the live config is now `config/nextoutfit-vite.php` (the dead `yournext-vite.php` duplicate was removed and its 3 call sites repointed).
- **Installer** locale picker trimmed from 21 languages to English-only (the only locale that actually ships).
- Added **Docker** (dev + prod flavors) and a **CI/CD deploy pipeline** — see [DEPLOYMENT.md](DEPLOYMENT.md).

### Known follow-ups (handed to the new developer)

- **Test suite is not runnable as-is.** Package-level Pest test suites exist (`packages/Frooxi/*/tests/`), but there is **no root `phpunit.xml`** wiring them together, and a number of remaining test files for *live* features still `use` classes from removed packages (e.g. `Shop/tests/Feature/Product/Prices/*`, `Shop/tests/Feature/Checkout/CartTest.php`, `Core/tests/Concerns/CoreAssertions.php`, `Admin/tests/Feature/Reporting/ProductReportTest.php`). These need a working test runner to fix safely (remove the dead imports, confirm the rest passes) — they were intentionally left rather than blindly deleted, since they cover real features. Standing up `phpunit.xml` + a green smoke suite around checkout/payment should be an early priority.
- **The `api/v1/admin` REST API has no auth guard** — see [api/admin.md](api/admin.md). Lock it down before exposing it beyond trusted tooling.
- **Inert Tax columns/comments**: tax-shaped DB columns (`tax_amount`, `tax_category_id`, …) and commented-out tax code blocks remain in `Cart.php`, `Core.php`, and the Product type classes. They're load-bearing no-ops (the checkout math reads the zeroed values), so cleaning them needs the test suite running first.
- **Redundant deps**: `laravel/cashier` (no billing code anywhere) and `predis/predis` (file cache + DB queue are actually used) are still in `composer.json` — safe to drop after a final confirm. Two PDF libraries are both genuinely used (`dompdf` for LTR, `mpdf` for RTL invoices), so keep both.
- **Webkul→Frooxi rename** is mostly complete but `app/Providers/AppServiceProvider.php` keeps a deliberate `morphMap` bridging old `Webkul\*` polymorphic type strings for any unmigrated historical rows — don't remove it until a DB check confirms no `Webkul\*` strings remain in morph columns.
