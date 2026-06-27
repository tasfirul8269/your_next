# API / Route Reference

Verified directly against the route files on 2026-06-26. Route names are Laravel route names (usable with `route('name')`), not URL slugs.

> **This is the route *index*** (method + path + controller). For **per-endpoint detail** — request parameters, validation rules, request/response JSON shapes, auth requirements — see the dedicated detailed references:
> - **[api/shop.md](api/shop.md)** — every Shop endpoint (cart, checkout, customer, catalog, payment callbacks).
> - **[api/admin.md](api/admin.md)** — the `api/v1/admin` REST API, including the ⚠️ unauthenticated-API security warning.
>
> Note (post-June-2026 cleanup): the `flatrate`/`free` shipping carriers and the `moneytransfer` payment method were removed — shipping is `customshipping` only, payment is `cashondelivery` / `sslcommerz` / `bkash`. Any `flatrate_flatrate`/`free_free` examples below are historical.

## Admin Routes

Admin URL prefix is `config('app.admin_url')` (`.env` `APP_ADMIN_URL`, default `admin`). So `orders` below means `/admin/orders` by default.

### Auth (`auth-routes.php`) — public, middleware: `web`, `PreventRequestsDuringMaintenance`

| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `/` | — | `Controller@redirectToLogin` |
| GET | `/login` | `admin.session.create` | `SessionController@create` |
| POST | `/login` | `admin.session.store` | `SessionController@store` |
| GET | `/two-factor/verify` | `admin.two_factor.verify.form` | `TwoFactorController@showVerifyForm` |
| POST | `/two-factor/verify` | `admin.two_factor.verifyTwoFactorCode` | `TwoFactorController@verifyTwoFactorCode` |
| GET | `/forget-password` | `admin.forget_password.create` | `ForgetPasswordController@create` |
| POST | `/forget-password` | `admin.forget_password.store` | `ForgetPasswordController@store` |
| GET | `/reset-password/{token}` | `admin.reset_password.create` | `ResetPasswordController@create` |
| POST | `/reset-password` | `admin.reset_password.store` | `ResetPasswordController@store` |

### Sales (`sales-routes.php`) — middleware: `web`, `admin`, `NoCacheMiddleware`, prefix `sales`

**Orders**
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `orders` | `admin.sales.orders.index` | `OrderController@index` |
| GET | `orders/create/{cartId}` | `admin.sales.orders.create` | `OrderController@create` |
| POST | `orders/create/{cartId}` | `admin.sales.orders.store` | `OrderController@store` |
| GET | `orders/view/{id}` | `admin.sales.orders.view` | `OrderController@view` |
| POST | `orders/cancel/{id}` | `admin.sales.orders.cancel` | `OrderController@cancel` |
| DELETE | `orders/delete/{id}` | `admin.sales.orders.delete` | `OrderController@destroy` |
| POST | `orders/mass-delete` | `admin.sales.orders.mass_delete` | `OrderController@massDestroy` |
| GET | `orders/reorder/{id}` | `admin.sales.orders.reorder` | `OrderController@reorder` |
| POST | `orders/comment/{order_id}` | `admin.sales.orders.comment` | `OrderController@comment` |
| POST | `orders/update-status/{id}` | `admin.sales.orders.update_status` | `OrderController@updateStatus` |
| GET | `orders/search` | `admin.sales.orders.search` | `OrderController@search` |

**Invoices**
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `invoices` | `admin.sales.invoices.index` | `InvoiceController@index` |
| GET | `invoices/create/{order_id}` | `admin.sales.invoices.create` | `InvoiceController@create` |
| POST | `invoices/create/{order_id}` | `admin.sales.invoices.store` | `InvoiceController@store` |
| GET | `invoices/view/{id}` | `admin.sales.invoices.view` | `InvoiceController@view` |
| DELETE | `invoices/delete/{id}` | `admin.sales.invoices.delete` | `InvoiceController@destroy` |
| POST | `invoices/mass-delete` | `admin.sales.invoices.mass_delete` | `InvoiceController@massDestroy` |
| POST | `invoices/send-duplicate-email/{id}` | `admin.sales.invoices.send_duplicate_email` | `InvoiceController@sendDuplicateEmail` |
| GET | `invoices/print/{id}` | `admin.sales.invoices.print` | `InvoiceController@printInvoice` |
| POST | `invoices/mass-update/state` | `admin.sales.invoices.mass_update.state` | `InvoiceController@massUpdateState` |

**Payment Methods**
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `payment-methods` | `admin.sales.payment_methods.index` | `PaymentMethodController@index` |
| POST | `payment-methods` | `admin.sales.payment_methods.store` | `PaymentMethodController@store` |

### Catalog (`catalog-routes.php`) — middleware: `web`, `admin`, `NoCacheMiddleware`, prefix `catalog`

**Attributes**
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `attributes` | `admin.catalog.attributes.index` | `AttributeController@index` |
| GET | `attributes/{id}/options` | `admin.catalog.attributes.options` | `AttributeController@getAttributeOptions` |
| GET | `attributes/create` | `admin.catalog.attributes.create` | `AttributeController@create` |
| POST | `attributes/create` | `admin.catalog.attributes.store` | `AttributeController@store` |
| GET | `attributes/edit/{id}` | `admin.catalog.attributes.edit` | `AttributeController@edit` |
| PUT | `attributes/edit/{id}` | `admin.catalog.attributes.update` | `AttributeController@update` |
| DELETE | `attributes/edit/{id}` | `admin.catalog.attributes.delete` | `AttributeController@destroy` |
| POST | `attributes/mass-delete` | `admin.catalog.attributes.mass_delete` | `AttributeController@massDestroy` |

**Attribute Families**
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `families` | `admin.catalog.families.index` | `AttributeFamilyController@index` |
| GET | `families/create` | `admin.catalog.families.create` | `AttributeFamilyController@create` |
| POST | `families/create` | `admin.catalog.families.store` | `AttributeFamilyController@store` |
| GET | `families/edit/{id}` | `admin.catalog.families.edit` | `AttributeFamilyController@edit` |
| PUT | `families/edit/{id}` | `admin.catalog.families.update` | `AttributeFamilyController@update` |
| DELETE | `families/edit/{id}` | `admin.catalog.families.delete` | `AttributeFamilyController@destroy` |

**Categories**
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `categories` | `admin.catalog.categories.index` | `CategoryController@index` |
| GET | `categories/create` | `admin.catalog.categories.create` | `CategoryController@create` |
| POST | `categories/create` | `admin.catalog.categories.store` | `CategoryController@store` |
| GET | `categories/edit/{id}` | `admin.catalog.categories.edit` | `CategoryController@edit` |
| PUT | `categories/edit/{id}` | `admin.catalog.categories.update` | `CategoryController@update` |
| DELETE | `categories/edit/{id}` | `admin.catalog.categories.delete` | `CategoryController@destroy` |
| POST | `categories/mass-delete` | `admin.catalog.categories.mass_delete` | `CategoryController@massDestroy` |
| POST | `categories/mass-update` | `admin.catalog.categories.mass_update` | `CategoryController@massUpdate` |
| GET | `categories/search` | `admin.catalog.categories.search` | `CategoryController@search` |
| GET | `categories/tree` | `admin.catalog.categories.tree` | `CategoryController@tree` |

**Products**
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `products` | `admin.catalog.products.index` | `ProductController@index` |
| POST | `products/create` | `admin.catalog.products.store` | `ProductController@store` |
| POST | `products/copy/{id}` | `admin.catalog.products.copy` | `ProductController@copy` |
| GET | `products/edit/{id}` | `admin.catalog.products.edit` | `ProductController@edit` |
| PUT | `products/edit/{id}` | `admin.catalog.products.update` | `ProductController@update` |
| DELETE | `products/edit/{id}` | `admin.catalog.products.delete` | `ProductController@destroy` |
| PUT | `products/edit/{id}/inventories` | `admin.catalog.products.update_inventories` | `ProductController@updateInventories` |
| POST | `products/upload-file/{id}` | `admin.catalog.products.upload_link` | `ProductController@uploadLink` |
| POST | `products/upload-sample/{id}` | `admin.catalog.products.upload_sample` | `ProductController@uploadSample` |
| POST | `products/mass-update` | `admin.catalog.products.mass_update` | `ProductController@massUpdate` |
| POST | `products/mass-delete` | `admin.catalog.products.mass_delete` | `ProductController@massDestroy` |
| GET | `products/{id}/simple-customizable-options` | `admin.catalog.products.simple.customizable-options` | `SimpleController@customizableOptions` |
| GET | `products/{id}/configurable-options` | `admin.catalog.products.configurable.options` | `ConfigurableController@options` |
| GET | `products/search` | `admin.catalog.products.search` | `ProductController@search` |
| GET | `products/{id}/{attribute_id}` | `admin.catalog.products.file.download` | `ProductController@download` |
| GET | `/sync` | — | `ProductController@sync` |

### Customers (`customers-routes.php`) — middleware: `web`, `admin`, `NoCacheMiddleware`, prefix `customers`

| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `` | `admin.customers.customers.index` | `CustomerController@index` |
| GET | `view/{id}` | `admin.customers.customers.view` | `CustomerController@show` |
| POST | `create` | `admin.customers.customers.store` | `CustomerController@store` |
| GET | `search` | `admin.customers.customers.search` | `CustomerController@search` |
| GET | `login-as-customer/{id}` | `admin.customers.customers.login_as_customer` | `CustomerController@loginAsCustomer` |
| POST | `note/{id}` | `admin.customer.note.store` | `CustomerController@storeNotes` |
| PUT | `edit/{id}` | `admin.customers.customers.update` | `CustomerController@update` |
| POST | `mass-delete` | `admin.customers.customers.mass_delete` | `CustomerController@massDestroy` |
| POST | `mass-update` | `admin.customers.customers.mass_update` | `CustomerController@massUpdate` |
| POST | `{id}` | `admin.customers.customers.delete` | `CustomerController@destroy` |
| GET | `{id}/wishlist-items` | `admin.customers.customers.wishlist.items` | `WishlistController@items` |
| DELETE | `{id}/wishlist-items` | `admin.customers.customers.wishlist.items.delete` | `WishlistController@destroy` |
| POST | `{id}/cart/create` | `admin.customers.customers.cart.store` | `CartController@store` |
| GET | `{id}/cart/items` | `admin.customers.customers.cart.items` | `CartController@items` |
| DELETE | `{id}/cart/items` | `admin.customers.customers.cart.items.delete` | `CartController@destroy` |
| GET | `{id}/recent-order-items` | `admin.customers.customers.orders.recent_items` | `OrderController@recentItems` |
| GET | `{id}/addresses` | `admin.customers.customers.addresses.index` | `AddressController@index` |
| GET | `{id}/addresses/create` | `admin.customers.customers.addresses.create` | `AddressController@create` |
| POST | `{id}/addresses/create` | `admin.customers.customers.addresses.store` | `AddressController@store` |
| GET | `addresses/edit/{id}` | `admin.customers.customers.addresses.edit` | `AddressController@edit` |
| PUT | `addresses/edit/{id}` | `admin.customers.customers.addresses.update` | `AddressController@update` |
| POST | `addresses/default/{id}` | `admin.customers.customers.addresses.set_default` | `AddressController@makeDefault` |
| POST | `addresses/delete/{id}` | `admin.customers.customers.addresses.delete` | `AddressController@destroy` |
| GET | `reviews` | `admin.customers.customers.review.index` | `ReviewController@index` |
| GET | `reviews/edit/{id}` | `admin.customers.customers.review.edit` | `ReviewController@edit` |
| PUT | `reviews/edit/{id}` | `admin.customers.customers.review.update` | `ReviewController@update` |
| DELETE | `reviews/{id}` | `admin.customers.customers.review.delete` | `ReviewController@destroy` |
| POST | `reviews/mass-delete` | `admin.customers.customers.review.mass_delete` | `ReviewController@massDestroy` |
| POST | `reviews/mass-update` | `admin.customers.customers.review.mass_update` | `ReviewController@massUpdate` |
| GET | `groups` | `admin.customers.groups.index` | `CustomerGroupController@index` |
| POST | `groups/create` | `admin.customers.groups.store` | `CustomerGroupController@store` |
| PUT | `groups/edit` | `admin.customers.groups.update` | `CustomerGroupController@update` |
| DELETE | `groups/delete/{id}` | `admin.customers.groups.delete` | `CustomerGroupController@destroy` |

### Settings (`settings-routes.php`) — middleware: `web`, `admin`, `NoCacheMiddleware`, prefix `settings`

| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `` | `admin.settings.page.index` | `SettingsPageController@index` |
| POST | `` | `admin.settings.page.store` | `SettingsPageController@store` |
| GET | `channels` | `admin.settings.channels.index` | `ChannelController@index` |
| GET / POST | `channels/create` | `admin.settings.channels.create/store` | `ChannelController@create/store` |
| GET / PUT / DELETE | `channels/edit/{id}` | `admin.settings.channels.edit/update/delete` | `ChannelController@edit/update/destroy` |
| GET | `currencies` | `admin.settings.currencies.index` | `CurrencyController@index` |
| POST | `currencies/create` | `admin.settings.currencies.store` | `CurrencyController@store` |
| GET / PUT / DELETE | `currencies/edit(/{id})` | `admin.settings.currencies.edit/update/delete` | `CurrencyController@edit/update/destroy` |
| POST | `currencies/mass-delete` | `admin.settings.currencies.mass_delete` | `CurrencyController@massDestroy` |
| GET | `exchange-rates` | `admin.settings.exchange_rates.index` | `ExchangeRateController@index` |
| POST | `exchange-rates/create` | `admin.settings.exchange_rates.store` | `ExchangeRateController@store` |
| GET | `exchange-rates/edit/{id}` | `admin.settings.exchange_rates.edit` | `ExchangeRateController@edit` |
| GET | `exchange-rates/update-rates` | `admin.settings.exchange_rates.update_rates` | `ExchangeRateController@updateRates` |
| PUT / DELETE | `exchange-rates/edit(/{id})` | `admin.settings.exchange_rates.update/delete` | `ExchangeRateController@update/destroy` |
| GET / POST / PUT / DELETE | `locales(/create\|edit/{id})` | `admin.settings.locales.*` | `LocaleController@*` |
| GET / POST / PUT / DELETE | `inventory-sources(/create\|edit/{id})` | `admin.settings.inventory_sources.*` | `InventorySourceController@*` |
| GET / POST / PUT / DELETE | `roles(/create\|edit/{id})` | `admin.settings.roles.*` | `RoleController@*` |
| GET / POST / PUT / DELETE | `users(/create\|edit/{id})` | `admin.settings.users.*` | `UserController@*` |
| PUT | `users/confirm` | `admin.settings.users.destroy` | `UserController@destroySelf` |
| GET / POST / PUT / DELETE | `themes(/edit/{id})` | `admin.settings.themes.*` | `ThemeController@*` |
| POST | `themes/mass-update` / `themes/mass-delete` | `admin.settings.themes.mass_update/mass_delete` | `ThemeController@massUpdate/massDestroy` |

### Storefront & Shipping (`storefront-routes.php`) — middleware: `web`, `admin`, `NoCacheMiddleware`, prefix `storefront`

| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET / POST / PUT / DELETE | `hero-carousel(/store\|update/{id}\|destroy/{id})` | `admin.storefront.hero_carousel.*` | `HeroCarouselController@*` |
| POST | `hero-carousel/mass-update` | `admin.storefront.hero_carousel.mass_update` | `HeroCarouselController@massUpdate` |
| GET / POST / PUT / DELETE | `flash-sale(/create\|store\|edit/{id}\|update/{id}\|destroy/{id})` | `admin.storefront.flash_sale.*` | `FlashSaleController@*` |
| PUT | `flash-sale/toggle/{id}` | `admin.storefront.flash_sale.toggle` | `FlashSaleController@toggleStatus` |
| POST | `flash-sale/mass-update` | `admin.storefront.flash_sale.mass_update` | `FlashSaleController@massUpdate` |
| GET / POST / PUT / DELETE | `shipping-methods(/store\|update/{id}\|destroy/{id})` | `admin.shipping_methods.*` | `ShippingMethodController@*` |

### Configuration (`configuration-routes.php`) — middleware: `web`, `admin`, `NoCacheMiddleware`

| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `configuration/search` | `admin.configuration.search` | `ConfigurationController@search` |
| POST | `configuration/cache-management/execute` | `admin.configuration.cache-management.execute` | `CacheManagementController@execute` |
| GET | `configuration/{slug?}/{slug2?}` | `admin.configuration.index` | `ConfigurationController@index` |
| POST | `configuration/{slug?}/{slug2?}` | `admin.configuration.store` | `ConfigurationController@store` |
| GET | `configuration/{slug?}/{slug2?}/{path}` | `admin.configuration.download` | `ConfigurationController@download` |

### Misc / REST (`rest-routes.php`) — middleware: `web`, `admin`, `NoCacheMiddleware`

| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `dashboard` | `admin.dashboard.index` | `DashboardController@index` |
| GET | `dashboard/stats` | `admin.dashboard.stats` | `DashboardController@stats` |
| GET | `datagrid/look-up` | `admin.datagrid.look_up` | `DataGridController@lookUp` |
| GET / POST / PUT / DELETE | `datagrid/saved-filters(/{id})` | `admin.datagrid.saved_filters.*` | `SavedFilterController@*` |
| POST | `tinymce/upload` | `admin.tinymce.upload` | `TinyMCEController@upload` |
| GET / PUT | `account` | `admin.account.edit/update` | `AccountController@edit/update` |
| GET | `two-factor/setup` | `admin.two_factor.setup` | `TwoFactorController@setup` |
| POST | `two-factor/enable` | `admin.two_factor.enable` | `TwoFactorController@enable` |
| GET | `two-factor/disable` | `admin.two_factor.disable` | `TwoFactorController@disable` |
| DELETE | `logout` | `admin.session.destroy` | `SessionController@destroy` |

### Admin REST API (`api.php`) — ⚠️ middleware: `api` ONLY, prefix `api/v1/admin` — **no `admin` session guard applied**

> This is the one part of the API surface worth a hard second look before shipping further. See [HANDOVER_QA.md](HANDOVER_QA.md) for why.

| Group | Endpoints |
|---|---|
| Dashboard | GET `dashboard/stats`, `dashboard/revenue-trend`, `dashboard/orders-overview`, `dashboard/top-products`, `dashboard/stock-alert`, `dashboard/customer-distribution` |
| Products | GET `attributes/options`, POST `attributes/color-options`, GET/POST `products`, GET/PUT/DELETE `products/{id}`, POST `products/{id}/images`, DELETE `products/{id}/images/{imageId}`, POST `products/{id}/videos` |
| Categories | POST `categories/reorder`, GET/POST `categories`, GET/PUT/DELETE `categories/{id}` |
| Customers | GET/POST `customers`, GET/PUT/DELETE `customers/{id}` |
| Orders | GET `orders`, GET `orders/{id}`, PUT `orders/{id}/status`, POST `orders/{id}/cancel`, DELETE `orders/{id}` |
| Sales | GET/POST `invoices`, GET `invoices/{id}`, GET `shipments`, GET `refunds` |
| Settings | GET `settings/users`, `settings/roles`, `settings/channels`, `settings/locales`, GET/PUT `settings/config`, GET/PUT `settings` |
| Storefront | GET/POST `storefront/hero-carousel`, POST `storefront/hero-carousel/reorder`, PUT/DELETE `storefront/hero-carousel/{id}`, PUT `storefront/hero-carousel/{id}/toggle` |
| Flash Sale | GET/POST `storefront/flash-sale`, PUT/DELETE `storefront/flash-sale/{id}`, PUT `storefront/flash-sale/{id}/toggle`, POST `storefront/flash-sale/reorder` |

### Admin DataGrids (`Admin/src/DataGrids/`)

`Catalog\AttributeDataGrid`, `Catalog\AttributeFamilyDataGrid`, `Catalog\CategoryDataGrid`, `Catalog\ProductDataGrid`, `Customers\CustomerDataGrid`, `Customers\GroupDataGrid`, `Customers\ReviewDataGrid`, `Customers\View\InvoiceDataGrid`, `Customers\View\OrderDataGrid`, `Customers\View\ReviewDataGrid`, `Sales\OrderDataGrid`, `Sales\OrderInvoiceDataGrid`, `Sales\OrderRefundDataGrid`, `Sales\OrderShipmentDataGrid`, `Sales\OrderTransactionDataGrid`, `Settings\ChannelDataGrid`, `Settings\CurrencyDataGrid`, `Settings\ExchangeRatesDataGrid`, `Settings\InventorySourcesDataGrid`, `Settings\LocalesDataGrid`, `Settings\RolesDataGrid`, `Settings\UserDataGrid`, `Storefront\FlashSaleProductDataGrid`, `Theme\ThemeDataGrid`.

---

## Shop Routes

Middleware base: `web`, `shop` (which itself applies `Theme`, `Locale`, `Currency` middleware), `PreventRequestsDuringMaintenance`.

### Storefront (`store-front-routes.php`) — no prefix

| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `/` | `shop.home.index` | `HomeController@index` |
| GET | `/contact-us` | `shop.home.contact_us` | `HomeController@contactUs` |
| GET | `/all-categories` | `shop.all-categories.index` | `HomeController@allCategories` |
| GET | `/flash-sale` | `shop.flash-sale.index` | `HomeController@flashSale` |
| POST | `/contact-us/send-mail` | `shop.home.contact_us.send_mail` | `HomeController@sendContactUsMail` |
| GET | `/search` | `shop.search.index` | `SearchController@index` |
| POST | `/search/upload` | `shop.search.upload` | `SearchController@upload` |
| GET | `/api/search` | `shop.search.suggestions` | `SearchController@suggestions` |
| POST | `/subscription` | `shop.subscription.store` | `SubscriptionController@store` |
| GET | `/subscription/{token}` | `shop.subscription.destroy` | `SubscriptionController@destroy` |
| GET | `/product/{id}/{attribute_id}` | `shop.product.file.download` | `ProductController@download` |
| * (fallback) | `*` | `shop.product_or_category.index` | `ProductsCategoriesProxyController@index` |

### Customer (`customer-routes.php`) — prefix `customer`

**Public (auth flows)**
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET/POST | `forgot-password` | `shop.customers.forgot_password.create/store` | `ForgotPasswordController@create/store` |
| GET | `reset-password/{token}` | `shop.customers.reset_password.create` | `ResetPasswordController@create` |
| POST | `reset-password` | `shop.customers.reset_password.store` | `ResetPasswordController@store` |
| GET/POST | `login` | `shop.customer.session.index/create` | `SessionController@index/store` |
| GET/POST | `register` | `shop.customers.register.index/store` | `RegistrationController@index/store` |
| GET | `verify-otp` | `shop.customers.verify-otp` | `RegistrationController@showOtpForm` |
| POST | `verify-otp` | `shop.customers.verify-otp.store` | `RegistrationController@verifyOtp` |
| POST | `resend-otp` | `shop.customers.resend-otp` | `RegistrationController@resendOtp` |

**Authenticated** (+ `customer` guard, `NoCacheMiddleware`)
| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `datagrid/look-up` | `shop.customer.datagrid.look_up` | `DataGridController@lookUp` |
| DELETE | `logout` | `shop.customer.session.destroy` | `SessionController@destroy` |
| GET | `account` | `shop.customers.account.index` | `CustomerController@account` |
| GET | `account/wishlist` | `shop.customers.account.wishlist.index` | `WishlistController@index` |
| GET | `account/profile` | `shop.customers.account.profile.index` | `CustomerController@index` |
| GET/POST | `account/profile/edit` | `shop.customers.account.profile.edit/update` | `CustomerController@edit/update` |
| POST | `account/profile/destroy` | `shop.customers.account.profile.destroy` | `CustomerController@destroy` |
| GET | `account/reviews` | `shop.customers.account.reviews.index` | `CustomerController@reviews` |
| GET/POST | `account/addresses(/create)` | `shop.customers.account.addresses.index/create/store` | `AddressController@*` |
| GET/PUT/PATCH/DELETE | `account/addresses/edit/{id}` & `delete/{id}` | `shop.customers.account.addresses.edit/update/update.default/delete` | `AddressController@*` |
| GET | `account/orders` | `shop.customers.account.orders.index` | `OrderController@index` |
| GET | `account/orders/view/{id}` | `shop.customers.account.orders.view` | `OrderController@view` |
| GET | `account/orders/reorder/{id}` | `shop.customers.account.orders.reorder` | `OrderController@reorder` |
| POST | `account/orders/cancel/{id}` | `shop.customers.account.orders.cancel` | `OrderController@cancel` |
| GET | `account/orders/print/Invoice/{id}` | `shop.customers.account.orders.print-invoice` | `OrderController@printInvoice` |

### Checkout (`checkout-routes.php`)

| Method | Path | Name | Controller@Action |
|---|---|---|---|
| GET | `checkout/cart` | `shop.checkout.cart.index` | `CartController@index` |
| GET | `checkout/onepage` | `shop.checkout.onepage.index` | `OnepageController@index` |
| GET | `checkout/onepage/success` | `shop.checkout.onepage.success` | `OnepageController@success` |
| GET | `checkout/bkash/pay` | `shop.bkash.pay` | `BkashController@pay` |
| GET | `checkout/bkash/callback` | `shop.bkash.callback` | `BkashController@callback` |
| GET | `checkout/bkash/cancel` | `shop.bkash.cancel` | `BkashController@cancel` |
| GET | `checkout/bkash/failure` | `shop.bkash.failure` | `BkashController@failure` |
| GET | `checkout/sslcommerz/pay` | `shop.sslcommerz.pay` | `SSLCommerzController@pay` |
| POST | `checkout/sslcommerz/success` | `shop.sslcommerz.success` | `SSLCommerzController@success` |
| POST | `checkout/sslcommerz/fail` | `shop.sslcommerz.fail` | `SSLCommerzController@fail` |
| POST | `checkout/sslcommerz/cancel` | `shop.sslcommerz.cancel` | `SSLCommerzController@cancel` |
| POST | `checkout/sslcommerz/ipn` | `shop.sslcommerz.ipn` | `SSLCommerzController@ipn` |

> The 4 POST SSLCommerz routes are server-to-server/redirect targets and must stay CSRF-exempt — verify they're listed in CSRF exclusions if you ever touch `VerifyCsrfToken` middleware config.

### Shop API (`api.php`) — prefix `api`

| Group | Endpoints |
|---|---|
| Hero Slides | GET `hero-slides` |
| Core | GET `core/countries`, `core/states` |
| Categories | GET `categories`, `categories/tree`, `categories/attributes`, `categories/attributes/{attribute_id}/options`, `categories/price-range/{id?}` |
| Products | GET `products`, `products/{id}/related`, `products/{id}/up-sell` |
| Reviews | GET `product/{id}/reviews`, POST `product/{id}/review`, GET `product/{id}/reviews/{review_id}/translate` |
| Cart | GET/POST/PUT/DELETE `checkout/cart`, DELETE `checkout/cart/selected`, POST `checkout/cart/move-to-wishlist`, POST/DELETE `checkout/cart/coupon`, POST `checkout/cart/estimate-shipping-methods`, GET `checkout/cart/cross-sell` |
| One-page checkout | GET `checkout/onepage/summary`, POST `checkout/onepage/addresses`, `checkout/onepage/shipping-methods`, `checkout/onepage/payment-methods`, `checkout/onepage/orders` |
| Customer login (public) | POST `customer/login` |
| Customer addresses (`customer` guard) | GET/POST `customer/addresses`, PUT `customer/addresses/edit/{id?}` |
| Customer wishlist (`customer` guard) | GET/POST `customer/wishlist`, POST `customer/wishlist/{id}/move-to-cart`, DELETE `customer/wishlist/all`, DELETE `customer/wishlist/{id}` |

---

## Error responses (observed convention, not formally documented in code)

| Code | Meaning |
|---|---|
| 200 | Success |
| 204 | Success, no content (deletes) |
| 400 | Validation/malformed request |
| 401 | Unauthenticated (AJAX-expecting JSON requests get a JSON 401, others redirect to login) |
| 403 | Authenticated but blocked (e.g. inactive/unverified customer, bad credentials) |
| 404 | Not found |
| 500 | Unhandled server error |

There is **no API versioning beyond the `api/v1/admin` prefix on the admin REST API** — the Shop API has no version segment at all.
