# Admin API & Panel Reference

The Admin package exposes two very different surfaces:

1. **The session-based admin panel** (`/admin/*`) — server-rendered Blade pages + DataGrid JSON, protected by the `admin` auth guard. Full route tables are in [API_REFERENCE.md](../API_REFERENCE.md#admin-routes).
2. **A JSON REST API** mounted at **`api/v1/admin/*`** (`packages/Frooxi/Admin/src/Routes/api.php`) — documented in detail below.

---

## ⚠️ SECURITY: the REST API has no auth guard

The entire `api/v1/admin` route group is declared as:

```php
Route::group(['middleware' => ['api']], function () {
```

There is **no `admin` guard** — only the stock `api` middleware. Every endpoint below (including all the data-mutating `POST`/`PUT`/`DELETE` routes) is reachable by **any unauthenticated HTTP client**. That means, as written, anyone who can reach the server can:

- read the full product catalog, customer PII (names, emails, phones, addresses), and every order;
- create/update/delete products, categories, **customers** (set passwords), orders, invoices;
- change order statuses, cancel orders, edit store configuration;
- manage hero-carousel and flash-sale content.

**This must be locked down before the API is exposed to anything beyond trusted local tooling** — e.g. change the group to `['middleware' => ['api', 'admin']]` (session) or add Sanctum token auth, and re-test the front-end (the admin SPA that calls these). This is called out again in [HANDOVER_QA.md](../HANDOVER_QA.md). Until then, treat this API as **internal/trusted-network only**.

Common response envelope: list endpoints return `{ "data": [...], "meta": {...} }`; single-resource endpoints return `{ "data": {...} }`; mutations return `{ "data": {...}, "message": "..." }`. Errors return `{ "message": "..." }` (or `{ "error": "..." }` on the dashboard) with an appropriate HTTP status.

---

## Dashboard — `DashboardController` (read-only)

All take an optional `?period=` query (`today`, `7days`, `30days`, `year`, `all`).

| Method | Path | Returns |
|---|---|---|
| GET | `dashboard/stats` | `{ total_revenue, total_orders, total_customers, active_products, recent_orders[] }` |
| GET | `dashboard/revenue-trend` | `{ labels: [Jan..Dec], values: [12 floats] }` |
| GET | `dashboard/orders-overview` | `{ labels: [Mon..Sun], values: [7 ints] }` |
| GET | `dashboard/top-products` | `[{ product_id, name, quantity_sold, revenue }]` (top 4) |
| GET | `dashboard/stock-alert` | `[{ product_id, name, sku, quantity }]` (low stock, 5) |
| GET | `dashboard/customer-distribution` | `{ new, returning, guest }` |

Errors return `500` `{ error, file, line }` (note: leaks file paths — tighten before production).

---

## Products — `ProductController`

### GET `products`
Query: `status`, `search` (SKU or attribute text), `per_page`|`limit` (default 15). Returns paginated list; each item: `id, sku, type, status, name, price, images[], quantity, stock_status, created_at, updated_at`, plus `variants_count` + `variants[]` for configurable products. `meta`: `{ total, per_page, current_page, last_page }`.

### POST `products` — ⚠️ mutates, no auth
```php
'type' => 'required|in:simple,configurable,virtual,downloadable,grouped,bundle',
'sku'  => 'required|unique:products,sku',
```
Optional: `attribute_family_id`, `super_attributes`, `name`, `url_key`, `short_description`, `description`, `price`, `special_price`, `cost`, `weight`, `status`, `featured`, `new`, `meta_*`, `video_url`, `delivery_info`, `care_instructions`, `categories[]`, `quantity`, `locale`, `channel`. → `201 { data, message }`.

### GET `products/{id}`
Full product detail incl. `images[], videos[], categories[], super_attributes[], delivery_info, care_instructions`, etc. → `404 { message }` if missing.

### PUT `products/{id}` — ⚠️ mutates, no auth
No required fields; accepts the same optional set as create plus `removed_images[]`. → `{ data, message }`.

### DELETE `products/{id}` — ⚠️ mutates, no auth → `204`.

### POST `products/{id}/images` — ⚠️ mutates, no auth
`'image' => 'required|file|image|max:10240'`. → `201 { data, message }`.

### DELETE `products/{id}/images/{imageId}` — ⚠️ mutates, no auth → `204`.

### POST `products/{id}/videos` — ⚠️ mutates, no auth
`'video_url' => 'required|url'`. → `200 { message }`.

### GET `attributes/options` — `admin.api.attributes.options`
Returns `{ data: { size: [{id, admin_name, label, sort_order, swatch_value}], sleeve: [...] } }`.

### POST `attributes/color-options` — ⚠️ mutates, no auth
Body `name` (required), `swatch_value` (optional). → `{ data: {id, name, swatch_value} }`; `422` if name missing; `404` if the color attribute doesn't exist.

---

## Categories — `CategoryController`

### GET `categories`
Returns nested category tree (`id, name, slug, parent_id, position, status, logo_url, banner_url, products_count, children[]`). **Cached 3600s.**

### GET `categories/{id}` → full model; `404` if missing.

### POST `categories` — ⚠️ mutates, no auth
```php
'name'      => 'required|string|max:255',
'slug'      => 'required|string|unique:category_translations,slug',
'parent_id' => 'nullable|exists:categories,id',
'status'    => 'boolean',
```
Optional: `description, position, display_mode, meta_*, logo_path, banner_path, locale`. → `201 { data, message }`.

### PUT `categories/{id}` — ⚠️ mutates, no auth
`slug` unique-ignoring-self, `parent_id`, `status`. → `{ data, message }`; `404` if missing.

### DELETE `categories/{id}` — ⚠️ mutates, no auth
→ `204`; `422` if the category has children; `404` if missing.

### POST `categories/reorder` — ⚠️ mutates, no auth
```php
'categories'            => 'required|array',
'categories.*.id'       => 'required|exists:categories,id',
'categories.*.position' => 'required|integer',
```
→ `{ message }`.

---

## Customers — `CustomerController`

### GET `customers` — paginated (`?limit=`, default 10).
### GET `customers/{id}` — with `orders`, `addresses`; `404` if missing.

### POST `customers` — ⚠️ mutates, no auth (creates an account + hashes a password, **unauthenticated**)
```php
'first_name'    => 'required|string|max:255',
'last_name'     => 'required|string|max:255',
'email'         => 'required|email|unique:customers,email',
'password'      => 'required|string|min:6',
'gender'        => 'nullable|in:Male,Female,Other',
'date_of_birth' => 'nullable|date',
'phone'         => 'nullable|string|max:20',
'status'        => 'boolean',
```
→ `201 { data, message }`.

### PUT `customers/{id}` — ⚠️ mutates, no auth (can reset any customer's password). `email` unique-ignoring-self; same optional fields. → `{ data, message }`.

### DELETE `customers/{id}` — ⚠️ mutates, no auth → `204`.

---

## Orders — `OrderController`

### GET `orders` — paginated (`?limit=`, default 10), with items.
### GET `orders/{id}` — full order + `status_history[]`; `404` if missing.

### PUT `orders/{id}/status` — ⚠️ mutates, no auth
`'status' => 'required|string'`. Allowed transitions: `pending → processing|canceled`, `pending_payment → pending|canceled`, `processing → completed|canceled`, `fraud → canceled`; `completed`/`canceled`/`closed` are terminal. → `{ data, message }`; `422` on an illegal transition; `404` if missing.

### POST `orders/{id}/cancel` — ⚠️ mutates, no auth → `{ data, message }`; `422` if not cancelable.

### DELETE `orders/{id}` — ⚠️ mutates, no auth → `204`.

---

## Sales — `SalesController`

| Method | Path | Notes |
|---|---|---|
| GET | `invoices` | paginated (`?limit=`), `{ data, meta }` |
| GET | `invoices/{id}` | invoice + order + addresses + items; `404` if missing |
| POST | `invoices` ⚠️ | `'order_id' => 'required|exists:orders,id'`, `'invoice.items' => 'required|array'` → `201`; `422` if not invoiceable |
| GET | `shipments` | paginated |
| GET | `refunds` | paginated |

---

## Settings — `SettingController`

| Method | Path | Notes |
|---|---|---|
| GET | `settings/users` | paginated admin users |
| GET | `settings/roles` | paginated roles |
| GET | `settings/channels` | paginated channels |
| GET | `settings/locales` | paginated locales |
| GET | `settings/config` (or `settings`) | `{ store_name, store_email, store_phone, store_address, products_per_page, default_sort, show_out_of_stock, order_prefix, min_order_amount }` |
| PUT | `settings/config` (or `settings`) ⚠️ | validates `store_name` (string≤255), `store_email` (email), `products_per_page` (int 1–100), `min_order_amount` (numeric≥0) → `{ message }` |

---

## Storefront — `StorefrontController` (hero carousel)

| Method | Path | Notes |
|---|---|---|
| GET | `storefront/hero-carousel` | slides: `id, title, subtitle, link, sort_order, status, media_path, type, category_id, media_url, category_name` |
| POST | `storefront/hero-carousel` ⚠️ | `'title' => 'required'`, `'image' => 'required'` |
| PUT | `storefront/hero-carousel/{id}` ⚠️ | multipart; optional `media_file` (`file|max:51200`); `404` if missing |
| DELETE | `storefront/hero-carousel/{id}` ⚠️ | `404` if missing |
| PUT | `storefront/hero-carousel/{id}/toggle` ⚠️ | toggles `status` |
| POST | `storefront/hero-carousel/reorder` ⚠️ | `orders[].id` (exists:hero_slides), `orders[].sort_order` (int) |

---

## Flash Sale — `FlashSaleController`

| Method | Path | Notes |
|---|---|---|
| GET | `storefront/flash-sale` | products: `id, sku, name, description, price, discount_percentage, quantity, image_path, channel_id, sort_order, status, timestamps` |
| POST | `storefront/flash-sale` ⚠️ | `sku` (unique), `name`, `price` (numeric≥0), `discount_percentage` (int 1–99), `quantity` (nullable int≥0), `image_file` (nullable file≤10240) |
| PUT | `storefront/flash-sale/{id}` ⚠️ | same rules, `sku` unique-ignoring-self; `404` if missing |
| DELETE | `storefront/flash-sale/{id}` ⚠️ | `404` if missing |
| PUT | `storefront/flash-sale/{id}/toggle` ⚠️ | returns new `status` |
| POST | `storefront/flash-sale/reorder` ⚠️ | `orders[]` with `id` + `sort_order` |

---

## Session-based admin panel (not JSON)

Everything under `/admin/*` other than the REST API above is the **server-rendered admin panel**: controllers return Blade views or redirects, and listing pages fetch rows via DataGrid JSON (`admin/datagrid/...`). These are protected by the `admin` guard + `NoCacheMiddleware` and the ACL/Bouncer permission system (`packages/Frooxi/Admin/src/Config/acl.php`). The full route inventory (catalog, sales, customers, settings, storefront, configuration, dashboard, auth, 2FA) is in [API_REFERENCE.md](../API_REFERENCE.md#admin-routes); the controller groups are: Catalog (Product/Category/Attribute/AttributeFamily), Sales (Order/Invoice/Shipment/Refund/Transaction/PaymentMethod), Customers (Customer/Address/Review/CustomerGroup), Settings (Channel/Currency/ExchangeRate/InventorySource/Locale/Role/User/ShippingMethod/Theme/SettingsPage), Storefront (HeroCarousel/FlashSale), plus Dashboard, Configuration, CacheManagement, DataGrid, and User auth/2FA.
