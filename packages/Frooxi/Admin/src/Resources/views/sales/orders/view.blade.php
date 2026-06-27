<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.sales.orders.view.title', ['order_id' => $order->increment_id])
    </x-slot>

    <!-- Header -->
    <div class="grid">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            {!! view_render_event('frooxi.admin.sales.order.title.before', ['order' => $order]) !!}

            <div class="flex items-center gap-2.5">
                <h1 class="font-serif text-2xl font-bold text-gray-900">
                    @lang('admin::app.sales.orders.view.title', ['order_id' => $order->increment_id])
                </h1>

                <!-- Order Status -->
                <span class="label-{{ $order->status }} text-sm mx-1.5">
                    @lang("admin::app.sales.orders.view.$order->status")
                </span>
            </div>

            {!! view_render_event('frooxi.admin.sales.order.title.after', ['order' => $order]) !!}

            <!-- Back Button -->
            <a
                href="{{ route('admin.sales.orders.index') }}"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            >
                @lang('admin::app.account.edit.back-btn')
            </a>
        </div>
    </div>

    <div class="mt-5 flex-wrap items-center justify-between gap-x-1 gap-y-2">
        <div class="flex gap-1.5">
            {!! view_render_event('frooxi.admin.sales.order.page_action.before', ['order' => $order]) !!}

            @if (
                $order->canReorder()
                && bouncer()->hasPermission('sales.orders.create')
                && core()->getConfigData('sales.order_settings.reorder.admin')
            )
                <a
                    href="{{ route('admin.sales.orders.reorder', $order->id) }}"
                    class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    <span class="icon-cart text-2xl"></span>

                    @lang('admin::app.sales.orders.view.reorder')
                </a>
            @endif

            @if (
                $order->canCancel()
                && bouncer()->hasPermission('sales.orders.cancel')
            )
               <form
                    method="POST"
                    ref="cancelOrderForm"
                    action="{{ route('admin.sales.orders.cancel', $order->id) }}"
                >
                    @csrf
                </form>

                <div
                    class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                    @click="$emitter.emit('open-confirm-modal', {
                        message: '@lang('admin::app.sales.orders.view.cancel-msg')',
                        agree: () => {
                            this.$refs['cancelOrderForm'].submit()
                        }
                    })"
                >
                    <span
                        class="icon-cancel text-2xl"
                        role="presentation"
                        tabindex="0"
                    >
                    </span>

                    <a href="javascript:void(0);">
                        @lang('admin::app.sales.orders.view.cancel')
                    </a>
                </div>
            @endif

            @if (
                $order->canInvoice()
                && bouncer()->hasPermission('sales.invoices.create')
            )
                @include('admin::sales.invoices.create')
            @endif

            {!! view_render_event('frooxi.admin.sales.order.page_action.after', ['order' => $order]) !!}
        </div>

        <!-- Order details -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left Component -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                {!! view_render_event('frooxi.admin.sales.order.left_component.before', ['order' => $order]) !!}

                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex justify-between pb-3 border-b border-gray-50 mb-4">
                        <p class="text-base font-semibold text-gray-900">
                            @lang('Order Items') ({{ count($order->items) }})
                        </p>

                        <p class="text-base font-semibold text-gray-900">
                            @lang('admin::app.sales.orders.view.grand-total', ['grand_total' => core()->formatBasePrice($order->base_grand_total)])
                        </p>
                    </div>

                    <!-- Order items -->
                    <div class="grid">
                        {!! view_render_event('frooxi.admin.sales.order.list.before', ['order' => $order]) !!}

                        @foreach ($order->items as $item)
                            {!! view_render_event('frooxi.admin.sales.order.list.item.before', ['order' => $order, 'item' => $item]) !!}

                            <div class="flex justify-between gap-2.5 border-b border-gray-100 px-4 py-5">
                                <div class="flex gap-2.5">
                                    @if($item?->product?->base_image_url)
                                        <img
                                            class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded"
                                            src="{{ $item?->product->base_image_url }}"
                                        >
                                    @else
                                        <div class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert">
                                            <img src="{{ frooxi_asset('images/product-placeholders/front.svg') }}">

                                            <p class="absolute bottom-1.5 w-full text-center text-[6px] font-semibold text-gray-400">
                                                @lang('admin::app.sales.invoices.view.product-image')
                                            </p>
                                        </div>
                                    @endif

                                    <div class="grid place-content-start gap-1.5">
                                        <p
                                            class="break-all text-sm font-semibold text-gray-800"
                                            v-pre
                                        >
                                            {{ $item->name }}
                                        </p>

                                        <div class="flex flex-col place-items-start gap-1.5">
                                            <p class="text-sm text-gray-600">
                                                @lang('admin::app.sales.orders.view.amount-per-unit', [
                                                    'amount' => core()->formatBasePrice($item->base_price),
                                                    'qty'    => $item->qty_ordered,
                                                ])
                                            </p>

                                            @if (isset($item->additional['attributes']))
                                                @foreach ($item->additional['attributes'] as $attribute)
                                                    <p
                                                        class="text-gray-600 dark:text-gray-300"
                                                        v-pre
                                                    >
                                                        @if (
                                                            ! isset($attribute['attribute_type'])
                                                            || $attribute['attribute_type'] !== 'file'
                                                        )
                                                            {{ $attribute['attribute_name'] }} : {{ $attribute['option_label'] }}
                                                        @else
                                                            {{ $attribute['attribute_name'] }} :

                                                            <a
                                                                href="{{ Storage::url($attribute['option_label']) }}"
                                                                class="text-blue-600 hover:underline"
                                                                download="{{ File::basename($attribute['option_label']) }}"
                                                            >
                                                                {{ File::basename($attribute['option_label']) }}
                                                            </a>
                                                        @endif
                                                    </p>
                                                @endforeach
                                            @endif

                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sku', ['sku' => $item->getTypeInstance()->getOrderedItem($item)->sku ])
                                            </p>

                                            <p class="text-gray-600 dark:text-gray-300">
                                                {{ $item->qty_ordered ? trans('admin::app.sales.orders.view.item-ordered', ['qty_ordered' => $item->qty_ordered]) : '' }}

                                                {{ $item->qty_invoiced ? trans('admin::app.sales.orders.view.item-invoice', ['qty_invoiced' => $item->qty_invoiced]) : '' }}

                                                {{ $item->qty_shipped ? trans('admin::app.sales.orders.view.item-shipped', ['qty_shipped' => $item->qty_shipped]) : '' }}

                                                {{ $item->qty_refunded ? trans('admin::app.sales.orders.view.item-refunded', ['qty_refunded' => $item->qty_refunded]) : '' }}

                                                {{ $item->qty_canceled ? trans('admin::app.sales.orders.view.item-canceled', ['qty_canceled' => $item->qty_canceled]) : '' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid place-content-start gap-1">
                                    <div class="">
                                        <p class="flex items-center justify-end gap-x-1 text-base font-semibold text-gray-800 dark:text-white">
                                            {{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }}
                                        </p>
                                    </div>

                                    <div class="flex flex-col place-items-start items-end gap-1.5">
                                        @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.price', ['price' => core()->formatBasePrice($item->base_price_incl_tax)])
                                            </p>
                                        @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.price-excl-tax', ['price' => core()->formatBasePrice($item->base_price)])
                                            </p>

                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.price-incl-tax', ['price' => core()->formatBasePrice($item->base_price_incl_tax)])
                                            </p>
                                        @else
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.price', ['price' => core()->formatBasePrice($item->base_price)])
                                            </p>
                                        @endif

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.tax', [
                                                'percent' => number_format($item->tax_percent, 2) . '%',
                                                'tax'     => core()->formatBasePrice($item->base_tax_amount)
                                            ])
                                        </p>

                                        @if ($order->base_discount_amount > 0)
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.discount', ['discount' => core()->formatBasePrice($item->base_discount_amount)])
                                            </p>
                                        @endif

                                        @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sub-total', ['sub_total' => core()->formatBasePrice($item->base_total_incl_tax)])
                                            </p>
                                        @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sub-total-excl-tax', ['sub_total' => core()->formatBasePrice($item->base_total)])
                                            </p>

                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sub-total-incl-tax', ['sub_total' => core()->formatBasePrice($item->base_total_incl_tax)])
                                            </p>
                                        @else
                                            <p class="text-gray-600 dark:text-gray-300">
                                                @lang('admin::app.sales.orders.view.sub-total', ['sub_total' => core()->formatBasePrice($item->base_total)])
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.list.item.after', ['order' => $order, 'item' => $item]) !!}
                        @endforeach

                        {!! view_render_event('frooxi.admin.sales.order.list.after', ['order' => $order]) !!}
                    </div>

                    <div class="mt-4 flex flex-auto justify-end p-4">
                        <div class="grid max-w-max gap-2 text-sm text-right">

                            {!! view_render_event('frooxi.admin.sales.order.view.subtotal.before') !!}

                            <!-- Sub Total -->
                            @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                <div class="flex w-full justify-between gap-x-5">
                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.summary-sub-total-incl-tax')
                                    </p>

                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_sub_total_incl_tax) }}
                                    </p>
                                </div>
                            @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                <div class="flex w-full justify-between gap-x-5">
                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.summary-sub-total-excl-tax')
                                    </p>

                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_sub_total) }}
                                    </p>
                                </div>

                                <div class="flex w-full justify-between gap-x-5">
                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.summary-sub-total-incl-tax')
                                    </p>

                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_sub_total_incl_tax) }}
                                    </p>
                                </div>
                            @else
                                <div class="flex w-full justify-between gap-x-5">
                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.summary-sub-total')
                                    </p>

                                    <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_sub_total) }}
                                    </p>
                                </div>
                            @endif

                            {!! view_render_event('frooxi.admin.sales.order.view.subtotal.after') !!}

                            {!! view_render_event('frooxi.admin.sales.order.view.shipping.before') !!}

                            <!-- Shipping And Handling -->
                            @if ($haveStockableItems = $order->haveStockableItems())
                                @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                    <div class="flex w-full justify-between gap-x-5">
                                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.shipping-and-handling-incl-tax')
                                        </p>

                                        <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                            {{ core()->formatBasePrice($order->base_shipping_amount_incl_tax) }}
                                        </p>
                                    </div>
                                @elseif (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                                    <div class="flex w-full justify-between gap-x-5">
                                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.shipping-and-handling-excl-tax')
                                        </p>

                                        <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                            {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                        </p>
                                    </div>

                                    <div class="flex w-full justify-between gap-x-5">
                                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.shipping-and-handling-incl-tax')
                                        </p>

                                        <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                            {{ core()->formatBasePrice($order->base_shipping_amount_incl_tax) }}
                                        </p>
                                    </div>
                                @else
                                    <div class="flex w-full justify-between gap-x-5">
                                        <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.shipping-and-handling')
                                        </p>

                                        <p class="font-semibold !leading-5 text-gray-600 dark:text-gray-300">
                                            {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                        </p>
                                    </div>
                                @endif
                            @endif

                            {!! view_render_event('frooxi.admin.sales.order.view.shipping.after') !!}

                            {!! view_render_event('frooxi.admin.sales.order.view.tax-amount.before') !!}

                            <!-- Tax Amount -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.summary-tax')
                                </p>

                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    {{ core()->formatBasePrice($order->base_tax_amount) }}
                                </p>
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.view.tax-amount.after') !!}

                            {!! view_render_event('frooxi.admin.sales.order.view.discount.before') !!}

                            <!-- Discount -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.summary-discount')
                                </p>

                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    {{ core()->formatBasePrice($order->base_discount_amount) }}
                                </p>
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.view.discount.after') !!}

                            {!! view_render_event('frooxi.admin.sales.order.view.grand-total.before') !!}

                            <!-- Grand Total -->
                            <div class="flex w-full justify-between gap-x-5 border-t border-gray-100 pt-2 mt-1">
                                <p class="text-base font-bold !leading-5 text-gray-900">
                                    @lang('admin::app.sales.orders.view.summary-grand-total')
                                </p>

                                <p class="text-base font-bold !leading-5 text-gray-900">
                                    {{ core()->formatBasePrice($order->base_grand_total) }}
                                </p>
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.view.grand-total.after') !!}

                            {!! view_render_event('frooxi.admin.sales.order.view.total-paid.before') !!}

                            <!-- Total Paid -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.total-paid')
                                </p>

                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    {{ core()->formatBasePrice($order->base_grand_total_invoiced) }}
                                </p>
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.view.total-paid.after') !!}

                            {!! view_render_event('frooxi.admin.sales.order.view.total-refunded.before') !!}

                            <!-- Total Refund -->
                            <div class="flex w-full justify-between gap-x-5">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.total-refund')
                                </p>

                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    {{ core()->formatBasePrice($order->base_grand_total_refunded) }}
                                </p>
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.view.total-refunded.after') !!}

                            {!! view_render_event('frooxi.admin.sales.order.view.total-due.before') !!}

                            <!-- Total Due -->
                            <div class="flex w-full justify-between gap-x-5 font-semibold">
                                <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.total-due')
                                </p>

                                @if($order->status !== 'canceled')
                                    <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice($order->base_total_due) }}
                                    </p>
                                @else
                                    <p class="!leading-5 text-gray-600 dark:text-gray-300">
                                        {{ core()->formatBasePrice(0.00) }}
                                    </p>
                                @endif
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.view.total-due.after') !!}

                        </div>
                    </div>
                </div>

                {!! view_render_event('frooxi.admin.sales.order.left_component.after', ['order' => $order]) !!}
            </div>

            <!-- Right Component -->
            <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                {!! view_render_event('frooxi.admin.sales.order.right_component.before', ['order' => $order]) !!}

                <!-- Customer and address information -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-900">
                            @lang('admin::app.sales.orders.view.customer')
                        </p>
                    </x-slot>

                    <x-slot:content v-pre>
                        <div class="{{ $order->billing_address ? 'pb-4' : '' }}">
                            <div class="flex flex-col gap-1.5">
                                <p 
                                    class="font-semibold text-gray-800 dark:text-white"
                                    v-pre
                                >
                                    {{ $order->customer_full_name }}
                                </p>

                                {!! view_render_event('frooxi.admin.sales.order.customer_full_name.after', ['order' => $order]) !!}

                                <p
                                    class="text-gray-600 dark:text-gray-300"
                                    v-pre
                                >
                                    {{ $order->customer_email }}
                                </p>

                                {!! view_render_event('frooxi.admin.sales.order.customer_email.after', ['order' => $order]) !!}

                                <p 
                                    class="text-gray-600 dark:text-gray-300"
                                    v-pre
                                >
                                    @lang('admin::app.sales.orders.view.customer-group') : {{ $order->is_guest ? core()->getGuestCustomerGroup()?->name : ($order->customer->group->name ?? '') }}
                                </p>

                                {!! view_render_event('frooxi.admin.sales.order.customer_group.after', ['order' => $order]) !!}
                            </div>
                        </div>

                        <!-- Billing Address -->
                        @if ($order->billing_address)
                            <span class="block w-full border-b border-gray-100"></span>

                            <div class="{{ $order->shipping_address ? 'pb-4' : '' }}">

                                <div class="flex items-center justify-between">
                                    <p class="py-4 text-base font-semibold text-gray-700">
                                        @lang('admin::app.sales.orders.view.billing-address')
                                    </p>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-4">
                                    @include ('admin::sales.address', ['address' => $order->billing_address])
                                </div>

                                {!! view_render_event('frooxi.admin.sales.order.billing_address.after', ['order' => $order]) !!}
                            </div>
                        @endif

                        <!-- Shipping Address -->
                        @if ($order->shipping_address)
                            <span class="block w-full border-b border-gray-100"></span>

                            <div class="flex items-center justify-between">
                                <p class="py-4 text-base font-semibold text-gray-700">
                                    @lang('admin::app.sales.orders.view.shipping-address')
                                </p>
                            </div>

                            <div class="rounded-lg bg-gray-50 p-4">
                                @include ('admin::sales.address', ['address' => $order->shipping_address])
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.shipping_address.after', ['order' => $order]) !!}
                        @endif
                    </x-slot>
                </x-admin::accordion>

                <!-- Order Information -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-900">
                            @lang('admin::app.sales.orders.view.order-information')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div class="flex w-full justify-start gap-5">
                            <div class="flex flex-col gap-y-1.5">
                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.order-date')
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.order-status')
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.channel')
                                </p>
                            </div>

                            <div class="flex flex-col gap-y-1.5">
                                {!! view_render_event('frooxi.admin.sales.order.created_at.before', ['order' => $order]) !!}

                                <!-- Order Date -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{core()->formatDate($order->created_at) }}
                                </p>

                                {!! view_render_event('frooxi.admin.sales.order.created_at.after', ['order' => $order]) !!}

                                <!-- Order Status -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{$order->status_label}}
                                </p>

                                {!! view_render_event('frooxi.admin.sales.order.status_label.after', ['order' => $order]) !!}

                                <!-- Order Channel -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{$order->channel_name}}
                                </p>

                                {!! view_render_event('frooxi.admin.sales.order.channel_name.after', ['order' => $order]) !!}
                            </div>
                        </div>
                    </x-slot>
                </x-admin::accordion>

                <!-- Payment and Shipping Information-->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-900">
                            @lang('admin::app.sales.orders.view.payment-and-shipping')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div>
                            <!-- Payment method -->
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ core()->getConfigData('sales.payment_methods.' . $order->payment->method . '.title') }}
                            </p>

                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.payment-method')
                            </p>

                            <!-- Currency -->
                            <p 
                                class="pt-4 font-semibold text-gray-800 dark:text-white"
                                v-pre
                            >
                                {{ $order->order_currency_code }}
                            </p>

                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.currency')
                            </p>

                            @php $additionalDetails = \Frooxi\Payment\Payment::getAdditionalDetails($order->payment->method); @endphp

                            <!-- Additional details -->
                            @if (! empty($additionalDetails))
                                <p 
                                    class="pt-4 font-semibold text-gray-800 dark:text-white"
                                    v-pre
                                >
                                    {{ $additionalDetails['title'] }}
                                </p>

                                <p 
                                    class="text-gray-600 dark:text-gray-300"
                                    v-pre
                                >
                                    {{ $additionalDetails['value'] }}
                                </p>
                            @endif

                            {!! view_render_event('frooxi.admin.sales.order.payment-method.after', ['order' => $order]) !!}
                        </div>

                        <!-- Shipping Method and Price Details -->
                        @if ($order->shipping_address)
                            <span class="mt-4 block w-full border-b dark:border-gray-800"></span>

                            <div class="pt-4">
                                <p 
                                    class="font-semibold text-gray-800 dark:text-white"
                                    v-pre
                                >
                                    {{ $order->shipping_title }}
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.shipping-method')
                                </p>

                                <p class="pt-4 font-semibold text-gray-800 dark:text-white">
                                    {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.shipping-price')
                                </p>
                            </div>

                            {!! view_render_event('frooxi.admin.sales.order.shipping-method.after', ['order' => $order]) !!}
                        @endif
                    </x-slot>
                </x-admin::accordion>

                {!! view_render_event('frooxi.admin.sales.order.right_component.after', ['order' => $order]) !!}
            </div>
        </div>
    </div>
</x-admin::layouts>
