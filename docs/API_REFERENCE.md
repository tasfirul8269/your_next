# Active route and API reference

This reference separates page routes from JSON or AJAX endpoints. It lists routes that are used by the current storefront and admin UI.

The route files contain additional definitions. Do not treat every route in the source tree as a supported public API without checking the UI and controller behavior.

## Route prefixes

| Area | Prefix | Source |
| --- | --- | --- |
| Shop pages | `/` | `packages/Frooxi/Shop/src/Routes/*.php` |
| Shop AJAX | `/api` | `packages/Frooxi/Shop/src/Routes/api.php` |
| Admin pages | `/{APP_ADMIN_URL}`. Default `/admin` | `packages/Frooxi/Admin/src/Routes/*.php` |
| Admin API | `/api/v1/admin` | `packages/Frooxi/Admin/src/Routes/api.php` |

## Shop page routes

| Method | Path | Purpose |
| --- | --- | --- |
| `GET` | `/` | Home page |
| `GET` | `/contact-us` | Contact page |
| `POST` | `/contact-us/send-mail` | Contact form submit |
| `GET` | `/all-categories` | Category index page |
| `GET` | `/flash-sale` | Flash sale page |
| `GET` | `/search` | Search results page |
| `POST` | `/search/upload` | Image search upload |
| `GET` | `/api/search` | Search suggestions |
| `POST` | `/subscription` | Newsletter subscription |
| `GET` | `/subscription/{token}` | Unsubscribe |
| `GET` | `/product/{id}/{attribute_id}` | Product file download |
| `GET` | `/checkout/cart` | Cart page |
| `GET` | `/checkout/onepage` | One-page checkout |
| `GET` | `/checkout/onepage/success` | Checkout success |
| `GET` | `/checkout/bkash/pay` | bKash payment start |
| `GET` | `/checkout/bkash/callback` | bKash callback |
| `GET` | `/checkout/bkash/cancel` | bKash cancel |
| `GET` | `/checkout/bkash/failure` | bKash failure |
| `GET` | `/checkout/sslcommerz/pay` | SSLCommerz payment start |
| `POST` | `/checkout/sslcommerz/success` | SSLCommerz success callback |
| `POST` | `/checkout/sslcommerz/fail` | SSLCommerz fail callback |
| `POST` | `/checkout/sslcommerz/cancel` | SSLCommerz cancel callback |
| `POST` | `/checkout/sslcommerz/ipn` | SSLCommerz IPN callback |
| `GET` | `/{slug}` | Product or category fallback route |

## Shop customer routes

| Method | Path | Auth | Purpose |
| --- | --- | --- | --- |
| `GET` | `/customer/login` | Public | Login page |
| `POST` | `/customer/login` | Public | Login submit |
| `GET` | `/customer/register` | Public | Register page |
| `POST` | `/customer/register` | Public | Register submit |
| `GET` | `/customer/verify-otp` | Public | OTP form |
| `POST` | `/customer/verify-otp` | Public | OTP verify |
| `POST` | `/customer/resend-otp` | Public | Resend OTP |
| `GET` | `/customer/forgot-password` | Public | Forgot password page |
| `POST` | `/customer/forgot-password` | Public | Forgot password submit |
| `GET` | `/customer/reset-password/{token}` | Public | Reset password page |
| `POST` | `/customer/reset-password` | Public | Reset password submit |
| `DELETE` | `/customer/logout` | Customer | Logout |
| `GET` | `/customer/account` | Customer | Account dashboard |
| `GET` | `/customer/account/profile` | Customer | Profile page |
| `GET` | `/customer/account/profile/edit` | Customer | Profile edit page |
| `POST` | `/customer/account/profile/edit` | Customer | Profile update |
| `POST` | `/customer/account/profile/destroy` | Customer | Profile deletion |
| `GET` | `/customer/account/addresses` | Customer | Address list |
| `GET` | `/customer/account/addresses/create` | Customer | Address create page |
| `POST` | `/customer/account/addresses/create` | Customer | Address create submit |
| `GET` | `/customer/account/addresses/edit/{id}` | Customer | Address edit page |
| `PUT` | `/customer/account/addresses/edit/{id}` | Customer | Address update |
| `PATCH` | `/customer/account/addresses/edit/{id}` | Customer | Make default address |
| `DELETE` | `/customer/account/addresses/delete/{id}` | Customer | Address delete |
| `GET` | `/customer/account/wishlist` | Customer | Wishlist page |
| `GET` | `/customer/account/reviews` | Customer | Customer reviews |
| `GET` | `/customer/account/orders` | Customer | Order list |
| `GET` | `/customer/account/orders/view/{id}` | Customer | Order detail |
| `GET` | `/customer/account/orders/reorder/{id}` | Customer | Reorder |
| `POST` | `/customer/account/orders/cancel/{id}` | Customer | Cancel order |
| `GET` | `/customer/account/orders/print/Invoice/{id}` | Customer | Print invoice |

## Shop API routes used by the storefront

See [api/shop.md](api/shop.md) for request notes.

| Method | Path | Auth | Used for |
| --- | --- | --- | --- |
| `GET` | `/api/categories/tree` | Public | Header category menu and category tabs |
| `GET` | `/api/categories/attributes` | Public | Filter definitions |
| `GET` | `/api/categories/attributes/{attribute_id}/options` | Public | Filter options |
| `GET` | `/api/categories/price-range/{id?}` | Public | Price slider range |
| `GET` | `/api/products` | Public | Search, category listing, flash sale products, product carousels |
| `GET` | `/api/products/{id}/related` | Public | Product detail related products |
| `GET` | `/api/products/{id}/up-sell` | Public | Product detail up-sell products |
| `GET` | `/api/product/{id}/reviews` | Public | Product reviews |
| `POST` | `/api/product/{id}/review` | Public route, controller validates customer state | Review submit |
| `GET` | `/api/product/{id}/reviews/{review_id}/translate` | Public | Review translation action |
| `GET` | `/api/checkout/cart` | Public | Mini cart and cart page refresh |
| `POST` | `/api/checkout/cart` | Public | Add item to cart |
| `PUT` | `/api/checkout/cart` | Public | Update cart quantities |
| `DELETE` | `/api/checkout/cart` | Public | Remove cart item |
| `DELETE` | `/api/checkout/cart/selected` | Public | Remove selected cart items |
| `POST` | `/api/checkout/cart/move-to-wishlist` | Public route, customer needed for wishlist | Move cart item to wishlist |
| `POST` | `/api/checkout/cart/coupon` | Public | Apply coupon |
| `DELETE` | `/api/checkout/cart/coupon` | Public | Remove coupon |
| `GET` | `/api/checkout/cart/cross-sell` | Public | Cart cross-sell carousel |
| `GET` | `/api/checkout/onepage/summary` | Public | Checkout summary |
| `POST` | `/api/checkout/onepage/addresses` | Public | Save checkout addresses |
| `POST` | `/api/checkout/onepage/shipping-methods` | Public | Save shipping method |
| `POST` | `/api/checkout/onepage/payment-methods` | Public | Save payment method |
| `POST` | `/api/checkout/onepage/orders` | Public | Place order |
| `POST` | `/api/customer/login` | Public | Checkout login |
| `GET` | `/api/customer/addresses` | Customer | Checkout saved addresses |
| `POST` | `/api/customer/addresses` | Customer | Create saved address during checkout |
| `PUT` | `/api/customer/addresses/edit/{id?}` | Customer | Update saved address during checkout |
| `GET` | `/api/customer/wishlist` | Customer | Wishlist status and wishlist page refresh |
| `POST` | `/api/customer/wishlist` | Customer | Add to wishlist |
| `POST` | `/api/customer/wishlist/{id}/move-to-cart` | Customer | Move wishlist item to cart |
| `DELETE` | `/api/customer/wishlist/all` | Customer | Clear wishlist |
| `DELETE` | `/api/customer/wishlist/{id}` | Customer | Remove wishlist item |

## Admin page routes

Admin routes are under `/{APP_ADMIN_URL}`. The default is `/admin`.

| Area | Main routes |
| --- | --- |
| Auth | `/login`, `/forget-password`, `/reset-password/{token}`, `/two-factor/verify` |
| Dashboard | `/dashboard`, `/dashboard/stats` |
| Catalog | `/catalog/products`, `/catalog/categories`, `/catalog/attributes`, `/catalog/families` |
| Sales | `/sales/orders`, `/sales/invoices`, `/sales/payment-methods` |
| Customers | `/customers`, `/customers/reviews`, `/customers/groups` |
| Storefront | `/storefront/hero-carousel`, `/storefront/flash-sale`, `/storefront/shipping-methods` |
| Settings & Config | `/settings`, `/configuration/search`, `/configuration/cache-management/execute` |
| Account | `/account`, `/two-factor/setup`, `/two-factor/enable`, `/two-factor/disable`, `/logout` |

## Admin AJAX and API routes used by the admin UI

See [api/admin.md](api/admin.md) for details.

| Method | Path | Middleware | Used for |
| --- | --- | --- | --- |
| `GET` | `/admin/dashboard/stats` | `web`, `admin` | Dashboard widgets |
| `GET` | `/admin/catalog/categories/tree` | `web`, `admin` | Category tree selectors |
| `GET` | `/admin/catalog/products/search` | `web`, `admin` | Product selectors |
| `GET` | `/admin/catalog/attributes/{id}/options` | `web`, `admin` | Attribute option editing |
| `POST` | `/admin/catalog/products/create` | `web`, `admin` | Product create modal |
| `POST` | `/admin/sales/orders/update-status/{id}` | `web`, `admin` | Order status update |
| `DELETE` | `/admin/sales/orders/delete/{id}` | `web`, `admin` | Order delete |
| `POST` | `/admin/sales/orders/mass-delete` | `web`, `admin` | Bulk order delete |
| `GET` | `/admin/customers/search` | `web`, `admin` | Create admin order for customer |
| `POST` | `/admin/customers/{id}/cart/create` | `web`, `admin` | Create admin cart |
| `GET` | `/admin/datagrid/look-up` | `web`, `admin` | DataGrid lookups |
| `GET` | `/admin/datagrid/saved-filters` | `web`, `admin` | Saved filters |
| `POST` | `/admin/datagrid/saved-filters` | `web`, `admin` | Save filter |
| `PUT` | `/admin/datagrid/saved-filters/{id}` | `web`, `admin` | Update filter |
| `DELETE` | `/admin/datagrid/saved-filters/{id}` | `web`, `admin` | Delete filter |
| `GET` | `/api/v1/admin/attributes/options` | `api` | Color or size option lookup |
| `POST` | `/api/v1/admin/attributes/color-options` | `api` | Create color option from product form |
| `DELETE` | `/api/v1/admin/categories/{id}` | `api` | Category delete from category tree UI |

The admin settings API routes in `packages/Frooxi/Admin/src/Routes/api.php` exist in source, but the current admin views do not call them. The protected admin settings pages use web routes instead.
