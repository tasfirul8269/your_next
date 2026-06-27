# Shop API details

The shop API is loaded by `Frooxi\Shop\Providers\ShopServiceProvider` with `web`, `shop`, and maintenance middleware. These endpoints are used by Blade and Vue components in `packages/Frooxi/Shop/src/Resources/views`.

## Catalog and filters

### `GET /api/products`

Used by search results, category pages, category tabs, flash sale sections, and product carousels.

Common query parameters come from the storefront UI:

| Parameter | Used for |
| --- | --- |
| `category_id` | Filter products by category |
| `sort` | Sorting |
| `limit` or `per_page` | Page size |
| `page` | Pagination |
| Attribute codes | Faceted filters |
| `price` or price bounds | Price filtering |
| `featured`, `new`, or flash-sale-related flags | Product sections |

Returns a product collection shaped for storefront cards.

### `GET /api/categories/tree`

Used by the desktop header, mobile header, category pages, and category tabs.

Returns the category hierarchy for navigation and category filter UI.

### `GET /api/categories/attributes`

Used by category and search filter panels.

Returns filterable attributes for the current listing context.

### `GET /api/categories/attributes/{attribute_id}/options`

Used when the storefront needs the options for a selected filter, such as color or size.

Supports pagination through query parameters used by the UI, such as `per_page`.

### `GET /api/categories/price-range/{id?}`

Used by search, category pages, and category tabs.

Returns the min and max price range for the current product set.

### `GET /api/products/{id}/related`

Used on the product detail page for related products.

### `GET /api/products/{id}/up-sell`

Used on the product detail page for up-sell products.

## Product reviews

### `GET /api/product/{id}/reviews`

Used by the product detail review section.

Returns paginated reviews for the product.

### `POST /api/product/{id}/review`

Used by the product detail review form.

The route is public, but review creation depends on controller validation and customer state.

### `GET /api/product/{id}/reviews/{review_id}/translate`

Used by the review section's translate action.

## Cart

### `GET /api/checkout/cart`

Used by the mini cart and cart page to refresh cart state.

### `POST /api/checkout/cart`

Used by product cards, product detail pages, category pages, search pages, and flash sale sections.

Common payload:

| Field | Purpose |
| --- | --- |
| `product_id` | Product to add |
| `quantity` | Quantity |
| `selected_configurable_option` | Selected variant for configurable products |
| Product option fields | Values required by the selected product type |

### `PUT /api/checkout/cart`

Used to update quantities.

Common payload:

| Field | Purpose |
| --- | --- |
| `qty` | Item quantity map or quantity value, depending on caller |

### `DELETE /api/checkout/cart`

Used to remove a cart item.

### `DELETE /api/checkout/cart/selected`

Used to remove selected cart items from the cart page.

### `POST /api/checkout/cart/move-to-wishlist`

Used to move a cart item into the wishlist.

Requires a customer session for the wishlist write to succeed.

### `POST /api/checkout/cart/coupon`

Used by the checkout coupon component.

Payload:

| Field | Purpose |
| --- | --- |
| `code` | Coupon code |

### `DELETE /api/checkout/cart/coupon`

Removes the applied coupon.

### `GET /api/checkout/cart/cross-sell`

Used by the cart page for cross-sell products.

## One-page checkout

### `GET /api/checkout/onepage/summary`

Used by the checkout page to refresh totals and selected checkout state.

### `POST /api/checkout/onepage/addresses`

Stores billing and shipping addresses for the active cart.

Used by guest checkout and logged-in checkout address forms.

### `POST /api/checkout/onepage/shipping-methods`

Stores the selected shipping method.

With the current shipping config, valid method values come from active rows in `shipping_methods` and use the `customshipping` carrier.

### `POST /api/checkout/onepage/payment-methods`

Stores the selected payment method.

Active payment method codes:

| Code | Flow |
| --- | --- |
| `cashondelivery` | Complete without redirect |
| `sslcommerz` | Redirect after order placement |
| `bkash` | Redirect after order placement |

### `POST /api/checkout/onepage/orders`

Creates the order from the active cart.

The response can include a redirect URL for gateway payment methods.

## Customer API

### `POST /api/customer/login`

Used by checkout login.

### `GET /api/customer/addresses`

Requires the `customer` middleware.

Used by checkout to load saved customer addresses.

### `POST /api/customer/addresses`

Requires the `customer` middleware.

Creates a saved customer address during checkout.

### `PUT /api/customer/addresses/edit/{id?}`

Requires the `customer` middleware.

Updates a saved customer address during checkout.

### `GET /api/customer/wishlist`

Requires the `customer` middleware.

Used by wishlist screens and product cards to read wishlist state.

### `POST /api/customer/wishlist`

Requires the `customer` middleware.

Adds a product to the wishlist.

### `POST /api/customer/wishlist/{id}/move-to-cart`

Requires the `customer` middleware.

Moves a wishlist item into the cart.

### `DELETE /api/customer/wishlist/all`

Requires the `customer` middleware.

Clears the wishlist.

### `DELETE /api/customer/wishlist/{id}`

Requires the `customer` middleware.

Deletes one wishlist item.

## Payment callback routes

These are web routes, not `/api` routes, but they are part of the checkout integration.

| Method | Path | Purpose |
| --- | --- | --- |
| `GET` | `/checkout/bkash/pay` | Start bKash payment |
| `GET` | `/checkout/bkash/callback` | bKash payment callback |
| `GET` | `/checkout/bkash/cancel` | bKash cancel redirect |
| `GET` | `/checkout/bkash/failure` | bKash failure redirect |
| `GET` | `/checkout/sslcommerz/pay` | Start SSLCommerz payment |
| `POST` | `/checkout/sslcommerz/success` | SSLCommerz success callback |
| `POST` | `/checkout/sslcommerz/fail` | SSLCommerz failure callback |
| `POST` | `/checkout/sslcommerz/cancel` | SSLCommerz cancel callback |
| `POST` | `/checkout/sslcommerz/ipn` | SSLCommerz IPN |

SSLCommerz callback paths are excluded from CSRF validation in `bootstrap/app.php`.

## Missing route used by storefront code

`packages/Frooxi/Shop/src/Resources/views/components/products/card.blade.php` posts logged-in compare actions to `/api/compare`. No matching route exists in the current shop route files. Guest compare uses `localStorage`, so this only affects logged-in compare behavior.
