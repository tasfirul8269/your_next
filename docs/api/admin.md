# Admin AJAX and API details

The admin panel mostly uses protected web routes under `/{APP_ADMIN_URL}`. The default prefix is `/admin`.

There is also an API route file mounted at `/api/v1/admin`, but the current admin views use only a small subset of it. Do not document the full API file as a handover contract unless the API is secured and reviewed.

## Protected admin web calls used by views

These routes use the `web` and `admin` middleware stack.

### Dashboard

| Method | Path | Used for |
| --- | --- | --- |
| `GET` | `/admin/dashboard/stats` | Dashboard stat cards and dashboard widgets |

### Catalog

| Method | Path | Used for |
| --- | --- | --- |
| `GET` | `/admin/catalog/categories/tree` | Category tree selectors in catalog and flash sale forms |
| `GET` | `/admin/catalog/products/search` | Product search selectors |
| `GET` | `/admin/catalog/attributes/{id}/options` | Attribute option editing |
| `GET` | `/admin/catalog/products/{id}/simple-customizable-options` | Simple product options in admin order creation |
| `GET` | `/admin/catalog/products/{id}/configurable-options` | Configurable product options in admin order creation |
| `POST` | `/admin/catalog/products/create` | Product create action from admin product screens |

### Sales

| Method | Path | Used for |
| --- | --- | --- |
| `POST` | `/admin/sales/orders/update-status/{id}` | Order status update |
| `DELETE` | `/admin/sales/orders/delete/{id}` | Order delete |
| `POST` | `/admin/sales/orders/mass-delete` | Bulk order delete |
| `POST` | `/admin/sales/orders/create/{cartId}` | Create admin order |

Some admin order creation Blade files reference `admin.sales.cart.*` route names, but the current `sales-routes.php` file does not define those routes. Treat the admin-created-order flow as needing route verification before handover.

### Customers

| Method | Path | Used for |
| --- | --- | --- |
| `GET` | `/admin/customers/search` | Customer selector for admin order creation |
| `POST` | `/admin/customers/create` | Customer create modal |
| `PUT` | `/admin/customers/edit/{id}` | Customer update |
| `POST` | `/admin/customers/{id}` | Customer delete |
| `GET` | `/admin/customers/{id}/wishlist-items` | Admin order creation from wishlist |
| `DELETE` | `/admin/customers/{id}/wishlist-items` | Remove wishlist item during admin order creation |
| `POST` | `/admin/customers/{id}/cart/create` | Create cart for admin order |
| `GET` | `/admin/customers/{id}/cart/items` | Customer cart items during admin order creation |
| `DELETE` | `/admin/customers/{id}/cart/items` | Remove customer cart item during admin order creation |
| `GET` | `/admin/customers/{id}/recent-order-items` | Recent order items during admin order creation |
| `POST` | `/admin/customers/{id}/addresses/create` | Create customer address |
| `PUT` | `/admin/customers/addresses/edit/{id}` | Update customer address |
| `POST` | `/admin/customers/addresses/default/{id}` | Set default customer address |
| `POST` | `/admin/customers/addresses/delete/{id}` | Delete customer address |
| `PUT` | `/admin/customers/reviews/edit/{id}` | Update customer review |

The admin order creation view also references compare-item routes, but matching routes are not present in `customers-routes.php`. Treat that part as needing route verification before handover.

### Storefront

| Method | Path | Used for |
| --- | --- | --- |
| `POST` | `/admin/storefront/hero-carousel/store` | Create hero slide |
| `PUT` | `/admin/storefront/hero-carousel/update/{id}` | Update hero slide |
| `DELETE` | `/admin/storefront/hero-carousel/destroy/{id}` | Delete hero slide |
| `POST` | `/admin/storefront/hero-carousel/mass-update` | Reorder or bulk update hero slides |
| `POST` | `/admin/storefront/flash-sale/store` | Create flash sale item |
| `PUT` | `/admin/storefront/flash-sale/update/{id}` | Update flash sale item |
| `DELETE` | `/admin/storefront/flash-sale/destroy/{id}` | Delete flash sale item |
| `PUT` | `/admin/storefront/flash-sale/toggle/{id}` | Toggle flash sale item |
| `POST` | `/admin/storefront/flash-sale/mass-update` | Reorder or bulk update flash sale items |
| `POST` | `/admin/storefront/shipping-methods/store` | Create custom shipping method |
| `PUT` | `/admin/storefront/shipping-methods/update/{id}` | Update custom shipping method |
| `DELETE` | `/admin/storefront/shipping-methods/destroy/{id}` | Delete custom shipping method |

### Settings, configuration, account, and DataGrid

| Method | Path | Used for |
| --- | --- | --- |
| `POST` | `/admin/settings` | Save unified settings page |
| `POST` | `/admin/settings/currencies/create` | Create currency |
| `PUT` | `/admin/settings/currencies/edit` | Update currency |
| `POST` | `/admin/settings/exchange-rates/create` | Create exchange rate |
| `PUT` | `/admin/settings/exchange-rates/edit` | Update exchange rate |
| `POST` | `/admin/settings/locales/create` | Create locale |
| `PUT` | `/admin/settings/locales/edit` | Update locale |
| `POST` | `/admin/settings/users/create` | Create admin user |
| `PUT` | `/admin/settings/users/edit` | Update admin user |
| `POST` | `/admin/settings/themes/store` | Create or update theme customization |
| `POST` | `/admin/configuration/cache-management/execute` | Clear configured caches |
| `GET` | `/admin/configuration/search` | Configuration search |
| `GET` | `/admin/datagrid/look-up` | DataGrid lookups |
| `GET` | `/admin/datagrid/saved-filters` | Read saved filters |
| `POST` | `/admin/datagrid/saved-filters` | Create saved filter |
| `PUT` | `/admin/datagrid/saved-filters/{id}` | Update saved filter |
| `DELETE` | `/admin/datagrid/saved-filters/{id}` | Delete saved filter |
| `PUT` | `/admin/account` | Update admin account |
| `GET` | `/admin/two-factor/setup` | Prepare two-factor setup |
| `POST` | `/admin/two-factor/enable` | Enable two-factor auth |
| `GET` | `/admin/two-factor/disable` | Disable two-factor auth |

## `/api/v1/admin` routes used by the admin UI

These routes use Laravel's `api` middleware. They are not protected by the `admin` session middleware in the route provider.

| Method | Path | Used for |
| --- | --- | --- |
| `GET` | `/api/v1/admin/attributes/options` | Attribute option lookup in product forms |
| `POST` | `/api/v1/admin/attributes/color-options` | Create a color option from product forms |
| `DELETE` | `/api/v1/admin/categories/{id}` | Delete a category from the category tree UI |

The admin API route file also defines products, orders, sales, settings, storefront, and dashboard API routes. The current admin views do not call those routes. They should remain internal until authentication, authorization, validation, and response contracts are reviewed.
