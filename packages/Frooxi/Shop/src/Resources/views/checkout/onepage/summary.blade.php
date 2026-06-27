{{-- Checkout Order Summary Panel --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">

    {{-- Header --}}
    <div style="padding:20px 24px;border-bottom:1px solid #f3f4f6;">
        <p style="font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:#9ca3af;margin:0;">
            @lang('shop::app.checkout.onepage.summary.cart-summary')
        </p>
    </div>

    {{-- Cart Items --}}
    <div style="padding:16px 24px;border-bottom:1px solid #f3f4f6;display:flex;flex-direction:column;gap:16px;">
        <div
            style="display:flex;gap:14px;align-items:flex-start;"
            v-for="item in cart.items"
        >
            {!! view_render_event('frooxi.shop.checkout.onepage.summary.item_image.before') !!}

            <div style="position:relative;flex-shrink:0;">
                <img
                    style="width:72px;height:72px;object-fit:cover;border-radius:8px;"
                    :src="item.base_image.small_image_url"
                    :alt="item.name"
                    width="72"
                    height="72"
                />
                <span style="position:absolute;top:-6px;right:-6px;background:#e30612;color:#fff;font-family:'Montserrat',sans-serif;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    @{{ item.quantity }}
                </span>
            </div>

            {!! view_render_event('frooxi.shop.checkout.onepage.summary.item_image.after') !!}

            <div style="flex:1;min-width:0;">
                {!! view_render_event('frooxi.shop.checkout.onepage.summary.item_name.before') !!}

                <p style="font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;color:#111;margin:0 0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    @{{ item.name }}
                </p>

                {!! view_render_event('frooxi.shop.checkout.onepage.summary.item_name.after') !!}

                <p style="font-family:'Montserrat',sans-serif;font-size:12px;color:#6b7280;margin:0;">
                    <template v-if="displayTax.prices == 'including_tax'">
                        @lang('shop::app.checkout.onepage.summary.price_and_qty', ['price' => '@{{ item.formatted_price_incl_tax }}', 'qty' => '@{{ item.quantity }}'])
                    </template>
                    <template v-else-if="displayTax.prices == 'both'">
                        @lang('shop::app.checkout.onepage.summary.price_and_qty', ['price' => '@{{ item.formatted_price_incl_tax }}', 'qty' => '@{{ item.quantity }}'])
                    </template>
                    <template v-else>
                        @lang('shop::app.checkout.onepage.summary.price_and_qty', ['price' => '@{{ item.formatted_price }}', 'qty' => '@{{ item.quantity }}'])
                    </template>
                </p>
            </div>
        </div>
    </div>

    {{-- Totals --}}
    <div style="padding:20px 24px;display:flex;flex-direction:column;gap:12px;">

        {{-- Sub Total --}}
        {!! view_render_event('frooxi.shop.checkout.onepage.summary.sub_total.before') !!}

        <template v-if="displayTax.subtotal == 'including_tax'">
            <div style="display:flex;justify-content:space-between;">
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.sub-total')</p>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#111;">@{{ cart.formatted_sub_total_incl_tax }}</p>
            </div>
        </template>
        <template v-else-if="displayTax.subtotal == 'both'">
            <div style="display:flex;justify-content:space-between;">
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.sub-total-excl-tax')</p>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#111;">@{{ cart.formatted_sub_total }}</p>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.sub-total-incl-tax')</p>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#111;">@{{ cart.formatted_sub_total_incl_tax }}</p>
            </div>
        </template>
        <template v-else>
            <div style="display:flex;justify-content:space-between;">
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.sub-total')</p>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#111;">@{{ cart.formatted_sub_total }}</p>
            </div>
        </template>

        {!! view_render_event('frooxi.shop.checkout.onepage.summary.sub_total.after') !!}

        {{-- Discount --}}
        {!! view_render_event('frooxi.shop.checkout.onepage.summary.discount_amount.before') !!}

        <div style="display:flex;justify-content:space-between;" v-if="cart.discount_amount && parseFloat(cart.discount_amount) > 0">
            <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.discount-amount')</p>
            <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#16a34a;">- @{{ cart.formatted_discount_amount }}</p>
        </div>

        {!! view_render_event('frooxi.shop.checkout.onepage.summary.discount_amount.after') !!}

        {{-- Coupon --}}
        {!! view_render_event('frooxi.shop.checkout.onepage.summary.coupon.before') !!}
        @include('shop::checkout.coupon')
        {!! view_render_event('frooxi.shop.checkout.onepage.summary.coupon.after') !!}

        {{-- Shipping --}}
        {!! view_render_event('frooxi.shop.checkout.onepage.summary.delivery_charges.before') !!}

        <template v-if="displayTax.shipping == 'including_tax'">
            <div style="display:flex;justify-content:space-between;">
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.delivery-charges')</p>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#111;">@{{ cart.formatted_shipping_amount_incl_tax }}</p>
            </div>
        </template>
        <template v-else-if="displayTax.shipping == 'both'">
            <div style="display:flex;justify-content:space-between;">
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.delivery-charges-excl-tax')</p>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#111;">@{{ cart.formatted_shipping_amount }}</p>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.delivery-charges-incl-tax')</p>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#111;">@{{ cart.formatted_shipping_amount_incl_tax }}</p>
            </div>
        </template>
        <template v-else>
            <div style="display:flex;justify-content:space-between;">
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;color:#6b7280;">@lang('shop::app.checkout.onepage.summary.delivery-charges')</p>
                <p style="font-family:'Montserrat',sans-serif;font-size:13px;font-weight:600;color:#111;">@{{ cart.formatted_shipping_amount }}</p>
            </div>
        </template>

        {!! view_render_event('frooxi.shop.checkout.onepage.summary.delivery_charges.after') !!}

        {{-- Grand Total --}}
        <div style="border-top:1.5px solid #111;padding-top:16px;margin-top:4px;">
            {!! view_render_event('frooxi.shop.checkout.onepage.summary.grand_total.before') !!}

            <div style="display:flex;justify-content:space-between;align-items:center;">
                <p style="font-family:'Montserrat',sans-serif;font-size:14px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#111;">
                    @lang('shop::app.checkout.onepage.summary.grand-total')
                </p>
                <p style="font-family:'Montserrat',sans-serif;font-size:18px;font-weight:700;color:#111;">
                    @{{ cart.formatted_grand_total }}
                </p>
            </div>

            {!! view_render_event('frooxi.shop.checkout.onepage.summary.grand_total.after') !!}
        </div>
    </div>
</div>
