{{--
    Deprecated: the separate "flash_sale_discount" input has been removed.
    Discounts are now entered in the single "Discount Percentage" product
    attribute field (code: discount_percentage), which the price engine,
    product card, and product detail page all read. This partial is kept as a
    no-op so any @include references stay valid; it intentionally renders nothing.
--}}
