# Package reference

All project packages live under `packages/Frooxi/`. This file lists the packages that are present and what they are used for in this repository.

## Package summary

| Package | Used for |
| --- | --- |
| `Admin` | Admin panel routes, controllers, views, dashboard, DataGrid screens, admin menu, ACL config, and admin-side AJAX |
| `Attribute` | Product attributes, attribute options, attribute families, and attribute groups |
| `Category` | Category tree, category translations, category filters, and category pages |
| `Checkout` | Cart, cart items, cart addresses, cart shipping rates, cart payment, checkout totals, wishlist movement |
| `Core` | Channels, locales, currencies, countries, states, config storage, newsletter subscribers, helpers, console commands |
| `Customer` | Customer accounts, OTP fields, customer addresses, customer groups, wishlist, compare items, customer notes |
| `DataGrid` | Admin tables, filters, saved filters, exports, mass actions |
| `Installer` | CLI installer, web installer, seeders, first-time setup helpers |
| `Inventory` | Inventory sources |
| `Payment` | Cash on Delivery, SSLCommerz, bKash, payment method registry |
| `Product` | Simple and configurable products, product attributes, images, videos, reviews, prices, inventory indices |
| `Sales` | Orders, order items, invoices, shipments, refunds, transactions, comments |
| `Shipping` | Admin-managed custom shipping methods and checkout rate collection |
| `Shop` | Customer storefront, product pages, category pages, search, checkout pages, customer account pages, shop API |
| `Theme` | Theme view resolution, theme customizations, Vite asset resolution |
| `User` | Admin users, roles, permissions, password resets, two-factor fields |

## Admin

Path: `packages/Frooxi/Admin`

The Admin package owns the back office. It loads login routes, protected admin web routes, and the `/api/v1/admin` API route file.

Main areas:

- Dashboard.
- Catalog management for products, categories, attributes, and attribute families.
- Sales management for orders, invoices, and payment methods.
- Customer management for customers, groups, addresses, notes, carts, wishlist items, and reviews.
- Storefront management for hero carousel, flash sale, and custom shipping methods.
- Settings and configuration pages.
- Admin account and two-factor authentication.

The admin menu is defined in `src/Config/menu.php`. Permission resources are defined in `src/Config/acl.php`.

## Shop

Path: `packages/Frooxi/Shop`

The Shop package owns the customer storefront and customer-facing AJAX endpoints.

Main areas:

- Home, contact, all categories, flash sale, search, product detail, and category pages.
- Customer registration, OTP verification, login, logout, password reset, account, profile, addresses, wishlist, reviews, orders, reorder, cancel, and invoice print.
- Cart page, mini cart, one-page checkout, checkout success page.
- SSLCommerz and bKash payment redirects and callbacks.
- JSON endpoints under `/api` for product listing, filters, cart, checkout, customer addresses, and wishlist.

## Product

Path: `packages/Frooxi/Product`

The active product types are:

| Type | Class |
| --- | --- |
| `simple` | `Frooxi\Product\Type\Simple` |
| `configurable` | `Frooxi\Product\Type\Configurable` |

The Product package stores product records, product attribute values, media, reviews, category relations, related products, cross-sell products, up-sell products, price indices, and inventory indices.

Use the indexer after product imports, price changes, stock changes, or bulk catalog updates:

```bash
php artisan indexer:index --type=price --type=inventory --type=flat --mode=full
```

## Attribute

Path: `packages/Frooxi/Attribute`

The Attribute package defines the EAV attribute system used by products and filters.

It includes:

- Attributes and translations.
- Attribute options and option translations.
- Attribute families.
- Attribute groups.
- Attribute group mappings.

The storefront filter UI reads category attributes and attribute options through shop API endpoints.

## Category

Path: `packages/Frooxi/Category`

The Category package manages the category tree. It uses nested set columns for hierarchy and translation tables for localized category fields.

Storefront category pages, header category menus, category filters, and product listing pages depend on this package.

## Checkout

Path: `packages/Frooxi/Checkout`

The Checkout package owns the cart state and one-page checkout data before an order is created.

It includes:

- `cart`
- `cart_items`
- `cart_addresses`
- `cart_payment`
- `cart_shipping_rates`
- `cart_item_inventories`

Guest carts use session state. Customer carts are tied to `customer_id`. When a customer logs in, `CustomerEventsHandler` can merge the guest cart into the customer cart.

## Sales

Path: `packages/Frooxi/Sales`

The Sales package owns persisted order data after checkout.

It includes:

- Orders and order items.
- Order addresses and payments.
- Invoices and invoice items.
- Shipments and shipment items.
- Refunds and refund items.
- Order transactions and comments.

Order and invoice increment IDs are generated by package sequencers.

## Payment

Path: `packages/Frooxi/Payment`

The active payment methods are defined in `src/Config/payment-methods.php`.

| Code | Class | Flow |
| --- | --- | --- |
| `cashondelivery` | `Frooxi\Payment\Payment\CashOnDelivery` | No redirect |
| `sslcommerz` | `Frooxi\Payment\Payment\SSLCommerz` | Redirect to SSLCommerz |
| `bkash` | `Frooxi\Payment\Payment\Bkash` | Redirect to bKash |

SSLCommerz runtime config is in `config/sslcommerz.php` and can be overridden by admin configuration values. SSLCommerz callback routes are excluded from CSRF validation in `bootstrap/app.php`.

bKash credentials are read by `Frooxi\Shop\Http\Controllers\BkashController`.

## Shipping

Path: `packages/Frooxi/Shipping`

The active carrier is `customshipping`.

| Code | Class | Source |
| --- | --- | --- |
| `customshipping` | `Frooxi\Shipping\Carriers\CustomShipping` | Active rows in `shipping_methods` |

The admin panel manages custom shipping methods under Storefront -> Shipping Methods. Checkout collects rates from active methods.

## Customer

Path: `packages/Frooxi/Customer`

The Customer package owns customer accounts and customer-related data.

It includes:

- Customer login data.
- OTP fields for verification.
- Customer addresses.
- Customer groups.
- Customer notes.
- Wishlist items.
- Compare items.

The storefront currently calls a missing `/api/compare` route for logged-in compare actions. Guest compare uses browser `localStorage`.

## User

Path: `packages/Frooxi/User`

The User package owns admin users and roles.

It includes:

- Admin accounts.
- Admin password resets.
- Roles.
- Role permission type and permission list.
- Two-factor columns on admin users.

## Core

Path: `packages/Frooxi/Core`

The Core package provides shared data and services:

- Channels.
- Locales.
- Countries and states.
- Currencies and exchange rates.
- Core config storage.
- Subscribers.
- Shared base models and helpers.
- Dynamic SMTP mailer.
- Maintenance mode overrides and secure headers.

Console commands from this package:

| Command | Purpose |
| --- | --- |
| `exchange-rate:update` | Updates exchange rates |
| `invoice:cron` | Runs overdue invoice reminders |
| `nextoutfit:version` | Prints the app version |
| `nextoutfit:translations:check` | Checks translation keys |

## Inventory

Path: `packages/Frooxi/Inventory`

The Inventory package defines inventory sources. Product stock per source is stored through Product package tables.

## DataGrid

Path: `packages/Frooxi/DataGrid`

The DataGrid package powers admin tables. It provides common behavior for search, filters, sorting, pagination, export, saved filters, and mass actions.

## Theme

Path: `packages/Frooxi/Theme`

The Theme package resolves active theme views and assets. It also stores theme customizations and translations.

## Installer

Path: `packages/Frooxi/Installer`

The Installer package provides:

- `php artisan nextoutfit:install`.
- A web installer route set.
- Environment and database setup helpers.
- Seeders for core data, categories, attributes, customers, inventory, shop data, products, and admin users.

The installer wipes and rebuilds the database. Use it only for a fresh setup.
