# Architecture

## Application shape

The project is a Laravel 12 modular monolith. There is one Laravel application, one database, and multiple domain packages.

The root `app/` directory is intentionally thin. Most behavior lives in `packages/Frooxi/`, where each package owns its models, repositories, views, translations, migrations, routes, config, or service providers.

The two main user surfaces are:

| Surface | Package | Default URL | Auth |
| --- | --- | --- | --- |
| Customer storefront | `packages/Frooxi/Shop` | `/` | Public pages plus the `customer` session guard |
| Admin panel | `packages/Frooxi/Admin` | `/admin` | `admin` session guard |

## Package registration

Packages are registered in two places:

- `bootstrap/providers.php` registers each package service provider.
- `config/concord.php` registers Concord module providers for model, proxy, and contract resolution.

Composer also maps the package namespaces directly:

```json
"Frooxi\\Product\\": "packages/Frooxi/Product/src"
```

The root `composer.json` uses a path repository:

```json
{
  "type": "path",
  "url": "packages/*/*",
  "options": {
    "symlink": true
  }
}
```

That means package code is edited directly in this repository. Run `composer dump-autoload` after adding new classes or changing namespace mappings.

## Model pattern

Domain entities usually use a three-part pattern:

| Part | Purpose |
| --- | --- |
| Contract | Interface used for type hints across packages |
| Model | Concrete Eloquent model |
| Proxy | Concord proxy used when another package needs the model |

Example:

```text
Frooxi\Product\Contracts\Product
Frooxi\Product\Models\Product
Frooxi\Product\Models\ProductProxy
```

Controllers usually work through repositories rather than querying Eloquent directly.

```text
Controller -> Repository -> Proxy or Model -> Database
```

## Route loading

Package service providers load routes instead of putting everything in the root `routes/web.php`.

Shop routes:

- `packages/Frooxi/Shop/src/Routes/store-front-routes.php`
- `packages/Frooxi/Shop/src/Routes/customer-routes.php`
- `packages/Frooxi/Shop/src/Routes/checkout-routes.php`
- `packages/Frooxi/Shop/src/Routes/api.php`
- `packages/Frooxi/Shop/src/Routes/web.php`

Admin routes:

- `packages/Frooxi/Admin/src/Routes/auth-routes.php`
- `packages/Frooxi/Admin/src/Routes/web-protected.php`
- `packages/Frooxi/Admin/src/Routes/catalog-routes.php`
- `packages/Frooxi/Admin/src/Routes/sales-routes.php`
- `packages/Frooxi/Admin/src/Routes/customers-routes.php`
- `packages/Frooxi/Admin/src/Routes/settings-routes.php`
- `packages/Frooxi/Admin/src/Routes/storefront-routes.php`
- `packages/Frooxi/Admin/src/Routes/configuration-routes.php`
- `packages/Frooxi/Admin/src/Routes/rest-routes.php`
- `packages/Frooxi/Admin/src/Routes/api.php`

The shop API routes are mounted under `/api` with web middleware. The admin API routes are mounted under `/api/v1/admin` with API middleware.

## Auth model

The app uses session authentication.

| Guard | Provider | Model |
| --- | --- | --- |
| `customer` | `customers` | `Frooxi\Customer\Models\Customer` |
| `admin` | `admins` | `Frooxi\User\Models\Admin` |

Customer registration uses OTP fields on the `customers` table. Admin users can use two-factor authentication through fields on the `admins` table.

Sanctum is installed, and user models have token-related support, but the current routes rely on session guards.

## Storefront request flow

Typical storefront browsing flow:

```text
Browser -> Shop route -> Shop controller -> Product or Category repository -> Blade view -> Shop Vite assets
```

Product listing pages use AJAX for filtering, sorting, price range, category tree, wishlist state, and cart actions.

## Checkout flow

The checkout flow uses the Checkout, Shipping, Payment, Sales, Product, Customer, and Inventory packages.

```text
Cart
-> cart items
-> billing and shipping addresses
-> custom shipping method
-> payment method
-> order
-> optional gateway redirect
-> gateway callback or Cash on Delivery completion
-> invoice and order status updates
```

The active payment methods are:

| Code | Flow |
| --- | --- |
| `cashondelivery` | No redirect |
| `sslcommerz` | Redirect to SSLCommerz and receive POST callbacks |
| `bkash` | Redirect to bKash and receive GET callback |

The active shipping carrier is `customshipping`. The admin panel manages rows in the `shipping_methods` table, and checkout exposes active rows as selectable rates.

## Product catalog

The active product types are defined in `packages/Frooxi/Product/src/Config/product_types.php`:

| Type | Class |
| --- | --- |
| `simple` | `Frooxi\Product\Type\Simple` |
| `configurable` | `Frooxi\Product\Type\Configurable` |

Product attribute data uses an EAV structure through the Attribute and Product packages. Storefront reads are optimized through `product_flat`, `product_price_indices`, and `product_inventory_indices`.

Use the indexer after imports or bulk product changes:

```bash
php artisan indexer:index --type=price --type=inventory --type=flat --mode=full
```

## Frontend builds

The root `package.json` exists, but the active package builds are separate.

| Area | Path | Notes |
| --- | --- | --- |
| Admin | `packages/Frooxi/Admin` | Vue 3, Alpine.js, Tailwind CSS, Chart.js, Vite |
| Shop | `packages/Frooxi/Shop` | Vue 3, Tailwind CSS, Vite |
| Installer | `packages/Frooxi/Installer` | Vue 3, Tailwind CSS, Vite |

The Docker development stack runs Admin and Shop Vite servers separately.

## Storage and media

`config/filesystems.php` defines `public`, `local`, `private`, `s3`, and `cloudinary` disks. The active disk comes from `FILESYSTEM_DISK`.

Product images, hero slides, theme assets, and uploaded files should be checked against the configured disk before migration or deployment.

## Data grids

Admin listing pages use the DataGrid package for search, filtering, sorting, pagination, export, mass actions, and saved filters.

Saved filters are stored in the `saved_filters` table and scoped to the grid and admin user.
