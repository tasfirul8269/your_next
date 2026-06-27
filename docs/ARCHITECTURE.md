# Architecture

## High-level shape

This is a **modular monolith**. There is effectively one Laravel app (`app/` is almost empty — just a thin `Controller`, an `EncryptCookies` middleware override, and `AppServiceProvider`). All real functionality lives in 16 self-contained packages under `packages/Frooxi/`, each registered twice:

1. **`bootstrap/providers.php`** — the package's main `XxxServiceProvider` (routes, views, translations, migrations, config publishing).
2. **`config/concord.php`** — the package's `ModuleServiceProvider` (registers its Eloquent models with Concord).

Two storefronts share the same codebase and database:
- **Admin** (`packages/Frooxi/Admin`) — back-office, served under `config('app.admin_url')` (default `/admin`).
- **Shop** (`packages/Frooxi/Shop`) — customer storefront, served at `/`.

Each has its own independent Vite build (`packages/Frooxi/Admin/package.json`, `packages/Frooxi/Shop/package.json`) producing separate JS/CSS bundles, plus a third independent build for `packages/Frooxi/Installer`.

## The three-file pattern: Contract / Model / Proxy

Every domain entity (e.g. a Product, a Customer, an Order) is represented by **three classes**, not one:

- **Contract** (`Contracts/ProductContract.php` or similar) — a PHP interface. Other packages type-hint against this, never against the concrete model.
- **Model** (`Models/Product.php`) — the actual Eloquent model, implements the Contract.
- **Proxy** (`Models/ProductProxy.php`) — a Concord-generated class that lets the *actual* model class used at runtime be swapped via config, without any other package's code changing. This is what makes it possible to override a core model from a custom package without editing core files.

When you need to reference a model from another package, you reference the **Proxy**, not the concrete class directly. `Repository::model()` returns the **Contract**, not the Eloquent class.

## Repository pattern (Prettus L5)

No controller queries Eloquent models directly. Every package has a `Repositories/` directory with classes extending `Frooxi\Core\Eloquent\Repository` (a thin wrapper around `prettus/l5-repository`). Controllers inject and call repositories; repositories call `model()` (resolved via the Proxy/Contract above) to get an Eloquent query.

```
Controller → Repository → Proxy → Contract → (concrete) Model → DB
```

## Path repositories for packages

`composer.json` declares:
```json
"repositories": [{"type": "path", "url": "packages/*/*", "options": {"symlink": true}}]
```
Packages are symlinked into the vendor autoloader rather than installed as real Composer packages. This means editing `packages/Frooxi/Product/src/...` takes effect immediately — no `composer update` needed. Only run `composer dump-autoload` when you add a *new* package or change PSR-4 mappings in the root `composer.json`.

## Route registration per package

Packages don't put everything in one `routes/web.php`. Each package's ServiceProvider loads multiple route files with different middleware stacks:

- **Admin**: `auth-routes.php` (public, login/forgot-password) vs. everything else (`catalog-routes.php`, `sales-routes.php`, `customers-routes.php`, `settings-routes.php`, `storefront-routes.php`, `configuration-routes.php`, `rest-routes.php`, `web-protected.php`) which require the `admin` auth middleware, plus a separate `api.php` mounted at `api/v1/admin` under the `api` middleware group (no admin guard — see [API_REFERENCE.md](API_REFERENCE.md) and flag this in [HANDOVER_QA.md](HANDOVER_QA.md)).
- **Shop**: `store-front-routes.php` (public catalog browsing, search, homepage), `customer-routes.php` (split public auth vs. `customer`-guarded account routes), `checkout-routes.php` (cart, one-page checkout, payment gateway callbacks), and `api.php` (AJAX endpoints under `/api`, mixed public/`customer`-guarded).

Full endpoint-by-endpoint tables are in [API_REFERENCE.md](API_REFERENCE.md).

## DataGrid abstraction

Almost every admin listing page (products, orders, customers, etc.) is backed by a class in `packages/Frooxi/Admin/src/DataGrids/` extending the base `DataGrid` class defined in the `Frooxi\DataGrid` package. Subclasses implement:

- `prepareQueryBuilder()` — the base Eloquent/query builder.
- `prepareColumns()` — calls `addColumn()` per column, each with `searchable`/`filterable`/`sortable`/`exportable` flags and an optional `closure` for custom rendering.
- `prepareActions()` / `prepareMassActions()` — per-row and bulk-row actions.

This gives every listing page consistent search/filter/sort/export/saved-filter behavior for free. Export uses `maatwebsite/excel`. Filter presets persist to a `SavedFilter` model per user per grid.

## Theming

`config/themes.php` registers themes for Admin and Shop with `assets_path`, `views_path`, and Vite hot-file/build-dir config. The `Frooxi\Theme` package's `Theme`/`Themes` classes resolve the active theme (with optional parent-theme inheritance) and a `ThemeViewFinder` walks child → parent when resolving Blade view paths. `ThemeCustomization` (+ translation) models store theme-wide editable content (carousels, footer links) per locale.

## Auth guards

Two completely separate auth systems, defined in `config/auth.php`:

| Guard | Driver | Provider model | Used by |
|---|---|---|---|
| `admin` | session | `Frooxi\User\Models\Admin` | Admin package |
| `customer` | session | `Frooxi\Customer\Models\Customer` | Shop package |

Both models have an `api_token` column (Sanctum-ready) but in practice **session-based auth is what's actually enforced** by the route middleware (`admin`, `customer` groups) — see [HANDOVER_QA.md](HANDOVER_QA.md) for why Sanctum doesn't seem to be load-bearing today.

## Cart → Order lifecycle

```
Cart (Checkout pkg, session or DB-backed)
  → CartItem, CartAddress, CartPayment, CartShippingRate
  → [One-page checkout: address → shipping method → payment method → place order]
  → Order (Sales pkg) created from Cart, OrderItem/OrderAddress/OrderPayment copied over
  → Invoice generated (auto for COD/MoneyTransfer per config, or after gateway confirms payment)
  → Shipment created against an InventorySource when fulfilled
  → Refund created against an Invoice if needed
```

Tax package has been removed from this fork, but tax-shaped columns (`tax_amount`, `price_incl_tax`, etc.) remain on `cart_items`, `order_items`, and shipping rate tables for backward compatibility / possible future re-introduction.

## Product types

`Frooxi\Product\Type\AbstractType` is subclassed per product type (`Simple`, `Configurable`, and others referenced by `config('product_types')`) to encapsulate type-specific behavior: variant handling, price calculation, cart validation, and invoicing logic. Attribute data is stored EAV-style in `product_attribute_values` (see [PACKAGES.md](PACKAGES.md#attribute)); a denormalized `product_flat` table plus `product_price_indices`/`product_inventory_indices` are kept in sync by the `indexer:index` console command for fast storefront queries.
