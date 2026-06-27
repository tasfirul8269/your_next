# Package Reference

All 16 packages live under `packages/Frooxi/`. This is the full set — if a package isn't listed here, it doesn't exist in this codebase (see [README.md](README.md) for packages that exist in the stale `.qoder` docs but not here).

---

## Admin

**Purpose**: Back-office UI — dashboard, catalog management, order management, customer management, storefront content management, system settings, ACL/roles.

No models of its own (it manages other packages' models via DataGrids/controllers). Owns:
- `Config/menu.php` — top-level admin menu: Dashboard, Catalog, Sales, Customers, Storefront, Settings.
- `Config/acl.php` — permission resource tree (`dashboard`, `sales.orders.*`, `sales.invoices.*`, `catalog.*`, `customers.*`, `settings.*`).
- 23 `DataGrids/*` classes (one per listing page — see [API_REFERENCE.md](API_REFERENCE.md) for the full list).
- 11 route files, including a separate `api/v1/admin` REST API (`api.php`) alongside the session-based `web-protected.php` etc.

Full route tables: [API_REFERENCE.md](API_REFERENCE.md#admin-routes).

---

## Shop

**Purpose**: Customer-facing storefront — homepage, search, product/category browsing, cart, one-page checkout, customer account area, flash sales.

No models of its own. Owns 5 route files: `store-front-routes.php`, `customer-routes.php`, `checkout-routes.php`, `api.php`, `web.php` (aggregator).

Full route tables: [API_REFERENCE.md](API_REFERENCE.md#shop-routes).

---

## Core

**Purpose**: Foundational infrastructure shared by every other package — locales, currencies/exchange rates, countries/states, multi-channel ("store") config, system config key/value store, newsletter subscribers, console commands, translation tooling.

**Models**: `Channel` (+`ChannelTranslation`), `Locale`, `Currency`, `CurrencyExchangeRate`, `Country` (+`CountryTranslation`), `CountryState` (+translation), `Address` (shared base — Customer and Order addresses extend/use this), `CoreConfig`, `SubscribersList`.

**Tables**: `locales`, `countries`, `country_translations`, `country_states`, `country_state_translations`, `currencies`, `currency_exchange_rates`, `channels`, `channel_translations`, `channel_inventory_sources`, `addresses`, `core_config`, `subscribers_list`.

**Console commands**:
| Signature | Purpose |
|---|---|
| `exchange-rate:update` | Pulls latest FX rates |
| `invoice:cron` | Sends overdue-invoice reminders |
| `nextoutfit:version` | Prints installed app version |
| `nextoutfit:translations:check` | Validates translation key parity across locale files (`--locale`, `--package`, `--details`); EN is canonical |

**Notable**: Implements the Proxy pattern's base machinery and `TranslatableModel`; has dynamic-SMTP service provider (per-channel mail settings, matches `MAIL_MAILER=frooxi-dynamic-smtp` in `.env.example`); has ElasticSearch facade/class wired up in code but **no `config/elasticsearch.php` exists** (see [README.md](README.md) gaps).

---

## Product

**Purpose**: Product catalog — types (simple/configurable/grouped/bundle), EAV attribute values, pricing, inventory linkage, media, reviews, downloadable products, search indexing.

**Models**: `Product`, `ProductAttributeValue`, `ProductFlat`, `ProductImage`, `ProductVideo`, `ProductInventory`, `ProductInventoryIndex`, `ProductPriceIndex`, `ProductGroupedProduct`, `ProductBundleOption(+Product)`, `ProductCustomizableOption(+Price)`, `ProductDownloadableLink`, `ProductDownloadableSample`, `ProductReview(+Attachment)`, `ProductCustomerGroupPrice`, `ProductSalableInventory`, `ProductOrderedInventory`.

**Tables**: `products`, `product_attribute_values`, `product_flat`, `product_inventories`, `product_categories`, `product_relations`, `product_super_attributes`, `product_up_sells`, `product_cross_sells`, `product_images`, `product_videos`, `product_reviews`, `product_grouped_products`, `product_bundle_options(_products)`, `product_customizable_options`, `product_downloadable_links(_samples)`, `product_customer_group_prices`, `product_price_indices`, `product_inventory_indices`.

**Console command**: `indexer:index --type=(inventory|price|flat|elastic) --mode=(selective|full)` — rebuilds denormalized index tables. The `elastic` type would fail today since there's no Elasticsearch config (see gaps).

**Notable**: `Type/AbstractType.php`, `Type/Simple.php`, `Type/Configurable.php` encapsulate per-type behavior. Multi-vendor field (`vendor_id`) exists on inventory tracking.

---

## Category

**Purpose**: Hierarchical product categorization via nested set (kalnoy/nestedset).

**Models**: `Category` (NestedSet trait, translatable), `CategoryTranslation`.

**Tables**: `categories` (`_lft`/`_rgt`/`parent_id`, image, status, `display_mode`), `category_translations` (name/description/slug/SEO per locale), `category_filterable_attributes` (faceted search config).

---

## Attribute

**Purpose**: EAV attribute system — definitions, families (grouped attribute sets), groups, options/swatches.

**Models**: `Attribute`, `AttributeTranslation`, `AttributeFamily`, `AttributeGroup`, `AttributeOption`, `AttributeOptionTranslation`.

**Tables**: `attributes` (type, required/unique/filterable/configurable/comparable flags, validation, swatch_type), `attribute_translations`, `attribute_families`, `attribute_groups`, `attribute_group_mappings`, `attribute_options`, `attribute_option_translations`.

**Notable**: Attribute `type` maps to a storage column on `product_attribute_values` (`text_value`, `float_value`, `integer_value`, `boolean_value`, `datetime_value`, `date_value`, JSON). `value_per_locale` / `value_per_channel` flags control whether a value varies by storefront context. Recent migrations (`create_sleeve_attribute`, `add_delivery_care_attributes`, `update_size_attribute_options`) show this system actively used for clothing-specific attributes (sleeve length, size, care instructions).

---

## Inventory

**Purpose**: Defines inventory sources (warehouses) referenced by `Product`'s stock tracking.

**Models**: `InventorySource`.

**Tables**: `inventory_sources` (code, name, address, contact, `priority`, lat/long, status).

**Notable**: Minimal package — just reference data; the actual stock-per-source linkage lives in Product's `product_inventories` table.

---

## Sales

**Purpose**: Orders, invoices, shipments, refunds, downloadable-link purchase tracking. Order lifecycle from creation through fulfillment.

**Models**: `Order`, `OrderItem`, `OrderAddress`, `OrderPayment`, `OrderTransaction`, `OrderComment`, `Invoice`, `InvoiceItem`, `Shipment`, `ShipmentItem`, `Refund`, `RefundItem`, `DownloadableLinkPurchased`.

**Tables**: `orders`, `order_items`, `invoices`, `invoice_items`, `shipments`, `shipment_items`, `order_payment`, `order_transactions`, `order_comments`, `refunds`, `refund_items`, `downloadable_link_purchased`.

**Notable**:
- `OrderSequencer`/`InvoiceSequencer` generate human-readable increment IDs.
- All money columns tracked in base currency, channel currency, *and* order currency with exchange rate snapshotting.
- `OrderItemRepository` allocates inventory on order creation and returns it on cancel/refund.
- Tax-shaped columns remain on `order_items` (`tax_category_id`, `applied_tax_rate`, `price_incl_tax`) even though the Tax package was removed — dead/inert today.
- Two recent migrations (`cleanup_sales_tables`, `clear_all_order_data`) indicate sales data was deliberately wiped at some point — confirm with the team before assuming any "production" order history is real.

---

## Checkout

**Purpose**: Shopping cart lifecycle — creation/merging (guest→customer), items, addresses, shipping/payment method selection.

**Models**: `Cart`, `CartItem`, `CartAddress`, `CartPayment`, `CartShippingRate`.

**Tables**: `cart`, `cart_items` (supports `parent_id` for configurable-product children, `additional` JSON for selected options), `cart_item_inventories`, `cart_addresses` (`address_type` discriminator + optional `parent_address_id` link to a saved customer address), `cart_payment`, `cart_shipping_rates`.

**Notable**:
- Guest carts live in `session()['cart']`; logged-in carts are queried by `customer_id`.
- `CustomerEventsHandler` listener merges a guest cart into the customer's cart on login.
- `collectTotals()` recalculates item/discount/shipping totals — tax calculation is effectively a no-op now (Tax package removed, code shows large commented-out tax logic).

---

## Payment

**Purpose**: Pluggable payment-method abstraction. Four methods registered in `Config/payment-methods.php`, each `['class', 'code', 'title', 'description', 'active', 'generate_invoice', 'sort']`:

| Method | Code | Flow | Notes |
|---|---|---|---|
| `Bkash.php` | `bkash` | **Redirect off-site** to bKash mobile-wallet portal | Routes: `shop.bkash.pay/callback/cancel/failure` (GET-based callback) |
| `SSLCommerz.php` | `sslcommerz` | **Redirect off-site** to SSLCommerz hosted payment page | Routes: `shop.sslcommerz.pay` (GET) + `success/fail/cancel/ipn` (POST, CSRF-exempt webhooks). Uses `Library/SSLCommerz/AbstractSslCommerz.php` (raw cURL POST to SSLCommerz API) and `SslCommerzNotification.php` (builds payment request, validates IPN). Config in `config/sslcommerz.php`: store credentials, sandbox vs. live API domain via `SSLCZ_TESTMODE`, callback URL paths — admin panel config (`getConfigData`) **overrides** `.env` values at runtime. |
| `CashOnDelivery.php` | `cashondelivery` | **No redirect** — only available if cart has exclusively stockable items | Can auto-generate invoice via `generate_invoice` config |
| `MoneyTransfer.php` | `moneytransfer` | **No redirect** — manual bank transfer | Surfaces a `mailing_address` config value as payment instructions; can auto-generate invoice |

**Listener**: `GenerateInvoice` (on `sales.order.save.after` event) — for COD/MoneyTransfer, auto-creates an invoice if `sales.payment_methods.<code>.generate_invoice` is true, using `<code>.invoice_status`/`<code>.order_status` config for resulting states.

---

## Shipping

**Purpose**: Shipping-method abstraction + rate calculation, three carriers registered in `Config/carriers.php`.

**Models**: `ShippingMethod` (admin-managed custom methods: name, description, price, status, sort_order).

**Tables**: `shipping_methods`.

**Carriers**:
| Carrier | Code | Behavior |
|---|---|---|
| `FlatRate` | `flatrate` | Fixed cost, optionally per-unit (`type` config) |
| `Free` | `free` | Always zero cost |
| `CustomShipping` | `customshipping` | One rate per active row in `shipping_methods` table |

**Core class** `Shipping.php`: `collectRates()` iterates all active carriers and saves resulting `CartShippingRate` rows; `getGroupedAllShippingRates()` feeds the checkout UI.

---

## Customer

**Purpose**: Shop-side customer accounts — auth (by **phone number**, not just email), addresses, wishlist, compare list, segmentation, notes.

**Models**: `Customer` (Authenticatable, Sanctum-ready, OTP fields), `CustomerAddress`, `CustomerGroup`, `CustomerNote`, `Wishlist`, `CompareItem`.

**Tables**: `customers` (phone unique, `otp_code`/`otp_expires_at`, `is_verified`, `is_suspended`, `subscribed_to_news_letter`, `customer_group_id`, `channel_id`), `customer_groups` (`is_user_defined` flag, default groups guest/general), `customer_password_resets`, `customer_notes`, `wishlist_items` (with `additional` JSON), `compare_items`.

**Notable**: Registration flow is OTP-based (`add_otp_fields_to_customers_table` migration) — see Shop's `customer-routes.php` (`verify-otp`, `resend-otp`).

---

## User

**Purpose**: Admin users, roles, permissions, two-factor auth.

**Models**: `Admin` (Authenticatable, Sanctum-ready, `two_factor_*` columns), `Role` (`permission_type`: `all` or `custom`, `permissions` JSON array of ACL keys).

**Tables**: `admins`, `roles`, `admin_password_resets`.

**ACL enforcement**: Admin package's `Config/acl.php` defines the permission key tree; a `Frooxi\User\Http\Middleware\Bouncer`-style check compares the current route's required ACL key against the logged-in admin's `Role.permissions`. `permission_type = 'all'` bypasses the check entirely.

---

## DataGrid

**Purpose**: Reusable list/table component (search, filter, sort, paginate, export, mass-actions, saved filters) used by virtually every Admin listing page. See [ARCHITECTURE.md](ARCHITECTURE.md#datagrid-abstraction) for the pattern. No models of its own beyond `SavedFilter`.

---

## Theme

**Purpose**: Theme registration/switching, Vite asset URL resolution, theme-wide editable content blocks.

**Models**: `ThemeCustomization` (translatable), `ThemeCustomizationTranslation` (JSON `options` column — carousels, footer links, etc.).

**Tables**: `theme_customizations`, `theme_customization_translations`.

See [ARCHITECTURE.md](ARCHITECTURE.md#theming) for the theme-resolution mechanism.

---

## Installer

**Purpose**: First-time setup, both CLI (`php artisan nextoutfit:install`) and a web wizard.

**CLI install flow** (in order):
1. Copy `.env.example` → `.env` if missing.
2. Prompt for app name/URL/timezone/locale/currency + allowed locales/currencies (skippable with `--skip-env-check`).
3. Prompt for DB connection (MySQL only), test it.
4. `key:generate`.
5. `db:wipe` then `migrate:fresh` (destructive — wipes any existing schema).
6. Seed core data (Core, Attribute, Category, Customer, Inventory, Shop, User, optionally sample Products).
7. `storage:link`.
8. Prompt for admin name/email/password (≥6 chars), optionally seed sample products + run `indexer:index --mode full` (skippable with `--skip-admin-creation`).
9. `optimize:clear`.
10. Prompt to star the GitHub repo (skippable with `--skip-github-star`).
11. Writes a `storage/installed` marker file.

**Web wizard routes** (`src/Routes/web.php`): `GET /install`, `POST /install/api/run-migration`, `POST /install/api/run-seeder`, `POST /install/api/seed-sample-products`, `POST /install/api/create-admin-user`.

**Note**: The installer's locale/currency prompts still list all the historical Bagisto locales (Arabic, Bengali, Catalan, German, English, Spanish, Persian, French, Hebrew, Hindi, Indonesian, Italian, Japanese, Dutch, Polish, Portuguese-BR, Russian, Sinhala, Turkish, Ukrainian, Chinese) even though only `en` translation files actually ship in `lang/` — selecting anything but English will produce missing-translation errors at runtime.
