# Shop API & Web Endpoint Reference

Detailed, code-grounded reference for the customer-facing storefront (`packages/Frooxi/Shop`). Every route, its auth requirement, request parameters with validation rules, and response shape is taken directly from the controllers, Form Requests, and API Resource classes.

## Conventions

- **Base middleware**: all Shop routes run under `web`, `shop`, `PreventRequestsDuringMaintenance`. The `shop` middleware group applies `Theme`, `Locale`, and `Currency` middleware.
- **Auth**: endpoints marked **Authenticated** require the `customer` session guard. Everything else is **Public**.
- **AJAX JSON**: the `/api/*` routes return JSON (mostly Laravel `JsonResource` envelopes). The non-`/api` customer routes are server-rendered Blade pages or redirects.
- **Money fields**: every monetary value comes in both a raw form (`grand_total`) and a localized form (`formatted_grand_total`). Tax fields exist but are always zero (the Tax package was removed).

---

## 1. Cart API — `CartController` (`/api/checkout/cart`)

All cart endpoints are **Public** (a guest cart lives in the session; a logged-in cart is keyed by `customer_id`).

### GET `/api/checkout/cart` — `shop.api.checkout.cart.index`
Returns the active cart. Validation: none.
```php
return new JsonResource(['data' => $cart ? new CartResource($cart) : null]);
```
**`CartResource` fields**: `id`, `is_guest`, `customer_id`, `items_count`, `items_qty`, `applied_taxes`, `tax_total`, `formatted_tax_total`, `sub_total`, `sub_total_incl_tax`, `formatted_sub_total`, `formatted_sub_total_incl_tax`, `coupon_code`, `discount_amount`, `formatted_discount_amount`, `shipping_method`, `shipping_amount`, `formatted_shipping_amount`, `grand_total`, `formatted_grand_total`, `items[]` (`CartItemResource`), `billing_address`, `shipping_address`, `have_stockable_items`, `payment_method`, `payment_method_title`.

### POST `/api/checkout/cart` — `shop.api.checkout.cart.store`
Add a product to the cart.
```php
'product_id' => 'required|integer|exists:products,id',
'is_buy_now' => 'integer|in:0,1',
'quantity'   => 'integer|min:1',
```
Request: `{ "product_id": 123, "quantity": 2, "is_buy_now": 0 }`. Additional configurable/option fields are passed through to `Cart::addProduct()`.
Success → `{ data: CartResource, message }` (plus `redirect` to onepage when `is_buy_now=1`).
Errors → `400` with `message` (insufficient inventory) or `400` with `redirect_uri` + `message`.

### PUT `/api/checkout/cart` — `shop.api.checkout.cart.update`
Update quantities. No validation. Request: `{ "items": [ {"id":1,"quantity":3}, ... ] }`. Returns updated `CartResource`.

### DELETE `/api/checkout/cart` — `shop.api.checkout.cart.destroy`
`'cart_item_id' => 'required|exists:cart_items,id'`. Returns updated `CartResource` + success message.

### DELETE `/api/checkout/cart/selected` — `shop.api.checkout.cart.destroy_selected`
Body `{ "ids": [1,2,3] }`. No validation. Returns updated cart.

### POST `/api/checkout/cart/move-to-wishlist` — `shop.api.checkout.cart.move_to_wishlist`
Body `{ "ids": [...], "qty": [...] }`. Returns updated cart.

### POST `/api/checkout/cart/estimate-shipping-methods` — `shop.api.checkout.cart.estimate_shipping`
Returns available shipping rates for the cart (used before login/checkout).

### POST `/api/checkout/cart/coupon` — `shop.api.checkout.cart.coupon.apply` — ⚠️ DISABLED
The coupon/promotion engine (CartRule package) was removed. The route still exists but applying a coupon is a no-op / not functional. Documented here so nobody assumes coupons work.

### DELETE `/api/checkout/cart/coupon` — `shop.api.checkout.cart.coupon.remove`
Removes any stored `coupon_code` from the cart.

### GET `/api/checkout/cart/cross-sell` — `shop.api.checkout.cart.cross-sell.index`
Returns cross-sell product suggestions for the current cart.

---

## 2. Checkout API — `OnepageController` (`/api/checkout/onepage`)

Public (the checkout flow works for guests). The flow is sequential: summary → addresses → shipping-methods → payment-methods → orders.

### GET `/api/checkout/onepage/summary` — `shop.checkout.onepage.summary`
Returns `new CartResource($cart)` (full cart snapshot for the checkout page).

### POST `/api/checkout/onepage/addresses` — `shop.checkout.onepage.addresses.store`
Validated by `CartAddressRequest`. It conditionally validates a `billing` block and, unless `billing.use_for_shipping` is set, a `shipping` block. Each address block requires: `first_name`, `last_name`, `email`, `address` (array, min 1), `city`, `state`, `country`, `postcode`, `phone` (validated by the `PhoneNumber` rule).
- Success (shipping needed) → `{ redirect: false, data: <shipping rates> }`
- Success (digital-only cart) → `{ redirect: false, data: <payment methods> }`
- Error → `{ redirect: true, redirect_url: <cart page> }`

### POST `/api/checkout/onepage/shipping-methods` — `shop.checkout.onepage.shipping_methods.store`
`'shipping_method' => 'required'`. The value is a method code — with the current carrier set that means `customshipping_<id>` (the `flatrate`/`free` carriers were removed). Success → `response()->json(Payment::getSupportedPaymentMethods())`. Error → `403` with `redirect_url`.

### POST `/api/checkout/onepage/payment-methods` — `shop.checkout.onepage.payment_methods.store`
`'payment' => 'required'`. Success → `{ cart: CartResource }`. Error → `403` with `redirect_url`.

### POST `/api/checkout/onepage/orders` — `shop.checkout.onepage.orders.store`
Places the order. No request body — relies on cart state from prior steps. Internally runs `validateOrder()` (checks: customer not suspended & active, minimum order amount, billing address present, shipping address present when stockable, shipping method selected, payment method selected).
- If the payment method needs an off-site redirect (SSLCommerz/bKash) → `{ redirect: true, redirect_url: <gateway url> }`
- On success → `{ redirect: true, redirect_url: route('shop.checkout.onepage.success') }`
- On coupon usage-limit failure → `{ redirect: false, message }` (catches `Frooxi\Checkout\Exceptions\CouponUsageLimitExceededException`)
- On validation failure → `500` with `message`

---

## 3. Customer Account API (Authenticated unless noted)

### POST `/api/customer/login` — `shop.api.customers.session.create` — Public
Validated by `LoginRequest`: `phone` (required, regex `^(\+?880|0)?1[3-9][0-9]{8}$`), `password` (required, min 6), wrapped in captcha validation. Note: **login is by phone number, not email.**
- Invalid credentials → `403` `{ message }`
- Account inactive → `403`
- Not verified → `403` (and queues resend cookies)
- Success → `200` empty body, fires `customer.after.login`

### Addresses — `AddressController` (Authenticated)
- GET `/api/customer/addresses` — `shop.api.customers.account.addresses.index`
- POST `/api/customer/addresses` — `shop.api.customers.account.addresses.store`
- PUT `/api/customer/addresses/edit/{id?}` — `shop.api.customers.account.addresses.update`

Validated by `AddressRequest` (fields: `company_name`, `first_name`, `last_name`, `address` array, `country`, `state`, `city`, `postcode`, `phone`, `email`).

### Wishlist — `WishlistController` (Authenticated)
- GET `/api/customer/wishlist` — list items
- POST `/api/customer/wishlist` — add (`product_id`)
- POST `/api/customer/wishlist/{id}/move-to-cart` — move item to cart
- DELETE `/api/customer/wishlist/{id}` — remove one
- DELETE `/api/customer/wishlist/all` — clear all

---

## 4. Catalog API (Public)

### Products — `ProductController`
- GET `/api/products` — `shop.api.products.index`. Query params: category filters, `?sort=`, `?limit=`, `?page=`, attribute filters. Returns a paginated product collection (storefront `product_flat` data).
- GET `/api/products/{id}/related` — related products.
- GET `/api/products/{id}/up-sell` — up-sell products.

### Categories — `CategoryController`
- GET `/api/categories` — flat list.
- GET `/api/categories/tree` — nested tree.
- GET `/api/categories/attributes` — filterable attributes for faceted search.
- GET `/api/categories/attributes/{attribute_id}/options` — options for one attribute.
- GET `/api/categories/price-range/{id?}` — min/max product price for the price-filter slider.

### Reviews — `ReviewController`
- GET `/api/product/{id}/reviews` — list reviews.
- POST `/api/product/{id}/review` — submit a review (validates `title`, `rating` 1–5, `comment`, optional image attachments).
- GET `/api/product/{id}/reviews/{review_id}/translate` — translate a review.

### Core lookup — `CoreController`
- GET `/api/core/countries` — country list.
- GET `/api/core/states` — state list (filtered by `?country_code=`).

### Hero slides — `HeroSlideController`
- GET `/api/hero-slides` — `shop.api.hero_slides.index` — active homepage carousel slides.

---

## 5. Storefront Web Pages (server-rendered Blade)

`store-front-routes.php` — Public unless noted.

| Method | Path | Name | Renders |
|---|---|---|---|
| GET | `/` | `shop.home.index` | Homepage |
| GET | `/contact-us` | `shop.home.contact_us` | Contact form |
| POST | `/contact-us/send-mail` | `shop.home.contact_us.send_mail` | Sends contact email |
| GET | `/all-categories` | `shop.all-categories.index` | Category index |
| GET | `/flash-sale` | `shop.flash-sale.index` | Flash sale page |
| GET | `/search` | `shop.search.index` | Search results |
| POST | `/search/upload` | `shop.search.upload` | Image-search upload |
| GET | `/api/search` | `shop.search.suggestions` | Autocomplete JSON |
| POST | `/subscription` | `shop.subscription.store` | Newsletter subscribe |
| GET | `/subscription/{token}` | `shop.subscription.destroy` | Unsubscribe |
| GET | `/product/{id}/{attribute_id}` | `shop.product.file.download` | Downloadable-product file |
| GET | `*` (fallback) | `shop.product_or_category.index` | Product or category page (slug router) |

---

## 6. Customer Auth & Account Web Pages

`customer-routes.php`, prefix `customer`.

### Registration — `RegistrationController` (Public)
- GET `/customer/register` → Blade `shop::customers.sign-up`.
- POST `/customer/register` → validated by `RegistrationRequest`:
  ```php
  'first_name' => 'string|required',
  'last_name'  => 'string|required',
  'phone'      => ['required','string','regex:/^(\+?880|0)?1[3-9][0-9]{8}$/','unique:customers,phone'],
  'password'   => 'confirmed|min:6|required',
  ```
  (wrapped in captcha). On success: generates an OTP via `OtpService::generateOtp($phone)`, sends it via SMS (SSLWireless SMS service), stores a pending registration in the session, and redirects to `shop.customers.verify-otp`.
- GET `/customer/verify-otp` → OTP entry form (shows masked phone).
- POST `/customer/verify-otp` → verifies the code, creates the customer, logs them in.
- POST `/customer/resend-otp` → regenerates and resends the OTP.

> **OTP delivery** is wired through `Frooxi\Customer\Services\SslWirelessSmsService` (an HTTP call to the SSLWireless SMS gateway, configured in `config/sslwireless.php`). In `mock` mode (the default) the OTP is logged instead of sent — set the SSLWireless env credentials and disable mock mode for real SMS.

### Login / Logout — `SessionController` (Public / Authenticated)
- GET `/customer/login` — login form.
- POST `/customer/login` — process login (phone + password).
- DELETE `/customer/logout` — (Authenticated) log out.

### Password reset — Public
- GET/POST `/customer/forgot-password` — `ForgotPasswordController` (request a reset link).
- GET `/customer/reset-password/{token}` + POST `/customer/reset-password` — `ResetPasswordController`.

### Account area — Authenticated (`customer` guard + `NoCacheMiddleware`)
- `/customer/account` — dashboard.
- `/customer/account/profile` (+ `/edit`, `/destroy`) — view/edit/delete profile.
- `/customer/account/addresses` (+ `/create`, `/edit/{id}`, `/delete/{id}`) — address book CRUD, PATCH sets default.
- `/customer/account/orders` (+ `/view/{id}`, `/reorder/{id}`, `/cancel/{id}`, `/print/Invoice/{id}`) — order history.
- `/customer/account/wishlist` — wishlist page.
- `/customer/account/reviews` — the customer's reviews.

---

## 7. Payment Gateway Callbacks — `checkout-routes.php`

These are the off-site redirect/return targets for the two hosted gateways. See [PACKAGES.md](../PACKAGES.md#payment) for the gateway internals.

### bKash — `BkashController` (GET-based)
| Method | Path | Name | Role |
|---|---|---|---|
| GET | `/checkout/bkash/pay` | `shop.bkash.pay` | Creates the bKash payment, redirects to the bKash portal |
| GET | `/checkout/bkash/callback` | `shop.bkash.callback` | Return URL; verifies & executes the payment, then places the order |
| GET | `/checkout/bkash/cancel` | `shop.bkash.cancel` | User cancelled |
| GET | `/checkout/bkash/failure` | `shop.bkash.failure` | Payment failed |

### SSLCommerz — `SSLCommerzController` (POST webhooks, CSRF-exempt)
| Method | Path | Name | Role |
|---|---|---|---|
| GET | `/checkout/sslcommerz/pay` | `shop.sslcommerz.pay` | Builds the payment request, redirects to the SSLCommerz hosted page |
| POST | `/checkout/sslcommerz/success` | `shop.sslcommerz.success` | Success return; validates the transaction, places the order |
| POST | `/checkout/sslcommerz/fail` | `shop.sslcommerz.fail` | Failure return |
| POST | `/checkout/sslcommerz/cancel` | `shop.sslcommerz.cancel` | Cancellation return |
| POST | `/checkout/sslcommerz/ipn` | `shop.sslcommerz.ipn` | Server-to-server IPN (Instant Payment Notification) validation |

> The four SSLCommerz POST routes must remain **CSRF-exempt** (they're called by SSLCommerz's servers / cross-site redirects). If you ever edit `VerifyCsrfToken`, keep `checkout/sslcommerz/*` in the exclusion list. Live store credentials configured in the admin panel override the `config/sslcommerz.php` / `.env` fallback values at runtime.
