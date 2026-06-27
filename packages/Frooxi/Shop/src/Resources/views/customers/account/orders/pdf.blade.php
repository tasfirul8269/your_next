<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}"
>
    <head>
        <meta
            http-equiv="Cache-control"
            content="no-cache"
        >

        <meta
            http-equiv="Content-Type"
            content="text/html; charset=utf-8"
        />

        @php
            $fontPath = [];

            if (app()->getLocale() == 'en' && $orderCurrencyCode == 'INR') {
                $fontFamily = [
                    'regular' => 'DejaVu Sans',
                    'bold'    => 'DejaVu Sans',
                ];
            } else {
                $fontFamily = [
                    'regular' => 'Arial, sans-serif',
                    'bold'    => 'Arial, sans-serif',
                ];
            }

            if (in_array(app()->getLocale(), ['ar', 'he', 'fa', 'tr', 'ru', 'uk'])) {
                $fontFamily = [
                    'regular' => 'DejaVu Sans',
                    'bold'    => 'DejaVu Sans',
                ];
            } elseif (app()->getLocale() == 'zh_CN') {
                $fontPath = [
                    'regular' => asset('fonts/NotoSansSC-Regular.ttf'),
                    'bold'    => asset('fonts/NotoSansSC-Bold.ttf'),
                ];

                $fontFamily = [
                    'regular' => 'Noto Sans SC',
                    'bold'    => 'Noto Sans SC Bold',
                ];
            } elseif (app()->getLocale() == 'ja') {
                $fontPath = [
                    'regular' => asset('fonts/NotoSansJP-Regular.ttf'),
                    'bold'    => asset('fonts/NotoSansJP-Bold.ttf'),
                ];

                $fontFamily = [
                    'regular' => 'Noto Sans JP',
                    'bold'    => 'Noto Sans JP Bold',
                ];
            } elseif (app()->getLocale() == 'hi_IN') {
                $fontPath = [
                    'regular' => asset('fonts/Hind-Regular.ttf'),
                    'bold'    => asset('fonts/Hind-Bold.ttf'),
                ];

                $fontFamily = [
                    'regular' => 'Hind',
                    'bold'    => 'Hind Bold',
                ];
            } elseif (app()->getLocale() == 'bn') {
                $fontPath = [
                    'regular' => asset('fonts/NotoSansBengali-Regular.ttf'),
                    'bold'    => asset('fonts/NotoSansBengali-Bold.ttf'),
                ];

                $fontFamily = [
                    'regular' => 'Noto Sans Bengali',
                    'bold'    => 'Noto Sans Bengali Bold',
                ];
            } elseif (app()->getLocale() == 'sin') {
                $fontPath = [
                    'regular' => asset('fonts/NotoSansSinhala-Regular.ttf'),
                    'bold'    => asset('fonts/NotoSansSinhala-Bold.ttf'),
                ];

                $fontFamily = [
                    'regular' => 'Noto Sans Sinhala',
                    'bold'    => 'Noto Sans Sinhala Bold',
                ];
            }

            // PDF-safe price formatter: uses currency code instead of symbol
            // to avoid Unicode characters (e.g. ৳) that PDF fonts cannot render
            $formatPdfPrice = function ($price, $currencyCode) {
                if (is_null($price)) {
                    $price = 0;
                }

                $currency = core()->getAllCurrencies()->where('code', $currencyCode)->first();
                $decimals = $currency ? ($currency->decimal ?? 2) : 2;
                $formatted = number_format((float) $price, $decimals, '.', ',');

                return $currencyCode . ' ' . $formatted;
            };
        @endphp

        <style type="text/css">
            @if (! empty($fontPath['regular']))
                @font-face {
                    src: url({{ $fontPath['regular'] }}) format('truetype');
                    font-family: {{ $fontFamily['regular'] }};
                }
            @endif

            @if (! empty($fontPath['bold']))
                @font-face {
                    src: url({{ $fontPath['bold'] }}) format('truetype');
                    font-family: {{ $fontFamily['bold'] }};
                    font-style: bold;
                }
            @endif

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: {{ $fontFamily['regular'] }};
            }

            body {
                font-size: 11px;
                color: #1a1a1a;
                font-family: "{{ $fontFamily['regular'] }}";
                background: #fff;
            }

            b, strong, th {
                font-family: "{{ $fontFamily['bold'] }}";
            }

            .page {
                padding: 40px 44px;
            }

            /* ---- HEADER ---- */
            .header-row {
                display: table;
                width: 100%;
                margin-bottom: 8px;
            }

            .header-left {
                display: table-cell;
                vertical-align: middle;
                width: 50%;
            }

            .header-left img {
                max-height: 48px;
                width: auto;
            }

            .header-right {
                display: table-cell;
                vertical-align: middle;
                width: 50%;
                text-align: right;
            }

            .header-right.rtl {
                text-align: left;
            }

            .invoice-title {
                font-family: "{{ $fontFamily['bold'] }}";
                font-size: 30px;
                color: #D63044;
                text-transform: uppercase;
                letter-spacing: 6px;
                margin: 0;
            }

            .header-rule {
                border: none;
                border-top: 1px solid #1a1a1a;
                margin: 20px 0 30px;
            }

            /* ---- INFO BLOCKS ---- */
            .info-row {
                display: table;
                width: 100%;
                margin-bottom: 6px;
            }

            .info-col {
                display: table-cell;
                width: 50%;
                vertical-align: top;
            }

            .info-col-right {
                text-align: right;
            }

            .info-col-right.rtl {
                text-align: left;
            }

            .info-label {
                font-family: "{{ $fontFamily['bold'] }}";
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 2px;
                color: #999;
                margin-bottom: 6px;
            }

            .info-value {
                font-size: 11px;
                color: #1a1a1a;
                line-height: 1.6;
            }

            .info-value div {
                margin-bottom: 1px;
            }

            .info-detail-row {
                margin-bottom: 10px;
            }

            .info-detail-label {
                font-family: "{{ $fontFamily['bold'] }}";
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                color: #999;
                margin-bottom: 4px;
            }

            .info-detail-value {
                font-size: 11px;
                color: #1a1a1a;
            }

            /* ---- SECTION LABEL ---- */
            .section-label {
                font-family: "{{ $fontFamily['bold'] }}";
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 2px;
                color: #999;
                margin-bottom: 6px;
                margin-top: 2px;
            }

            .section-rule {
                border: none;
                border-top: 1px solid #ddd;
                margin: 6px 0;
            }

            /* ---- TABLE ---- */
            table.items {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 24px;
            }

            table.items thead th {
                font-family: "{{ $fontFamily['bold'] }}";
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                color: #333;
                padding: 10px 12px;
                border-bottom: 1px solid #333;
                text-align: left;
                background: transparent;
            }

            table.items.rtl thead th {
                text-align: right;
            }

            table.items thead th:last-child,
            table.items thead th:nth-child(2),
            table.items thead th:nth-child(3) {
                text-align: right;
            }

            table.items.rtl thead th:last-child,
            table.items.rtl thead th:nth-child(2),
            table.items.rtl thead th:nth-child(3) {
                text-align: left;
            }

            table.items tbody td {
                padding: 12px 12px;
                border-bottom: 1px solid #ddd;
                text-align: left;
                vertical-align: top;
                color: #1a1a1a;
                font-size: 11px;
            }

            table.items.rtl tbody td {
                text-align: right;
            }

            table.items tbody td:last-child,
            table.items tbody td:nth-child(2),
            table.items tbody td:nth-child(3) {
                text-align: right;
            }

            table.items.rtl tbody td:last-child,
            table.items.rtl tbody td:nth-child(2),
            table.items.rtl tbody td:nth-child(3) {
                text-align: left;
            }

            .item-attr {
                font-size: 9px;
                color: #777;
                margin-top: 3px;
            }

            .small-text {
                font-size: 8px;
                color: #888;
            }

            /* ---- SUMMARY ---- */
            .summary-block {
                width: 100%;
                display: inline-block;
                margin-top: 8px;
            }

            .summary-table {
                float: right;
                width: 260px;
                border-collapse: collapse;
            }

            .summary-table.rtl {
                float: left;
            }

            .summary-table td {
                padding: 7px 10px;
                font-size: 11px;
                color: #1a1a1a;
                vertical-align: middle;
            }

            .summary-table td:first-child {
                text-align: left;
            }

            .summary-table.rtl td:first-child {
                text-align: right;
            }

            .summary-table td:last-child {
                text-align: right;
                white-space: nowrap;
            }

            .summary-table.rtl td:last-child {
                text-align: left;
            }

            .summary-table tr.grand-total td {
                border-top: 1px solid #333;
                padding-top: 10px;
                font-family: "{{ $fontFamily['bold'] }}";
            }

            /* ---- ADDRESS BLOCK ---- */
            .address-row {
                display: table;
                width: 100%;
                margin-bottom: 4px;
            }

            .address-col {
                display: table-cell;
                width: 50%;
                vertical-align: top;
                padding-right: 20px;
            }

            .address-col.rtl {
                padding-right: 0;
                padding-left: 20px;
            }

            .address-col:last-child {
                padding-right: 0;
                padding-left: 20px;
            }

            .address-col.rtl:last-child {
                padding-left: 0;
                padding-right: 20px;
            }
        </style>
    </head>

    <body dir="{{ core()->getCurrentLocale()->direction }}">
        <div class="page">
            <!-- ====== HEADER: Logo (left) + INVOICE (right) ====== -->
            <div class="header-row">
                <div class="header-left">
                    @if (core()->getConfigData('sales.invoice_settings.pdf_print_outs.logo'))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(Storage::url(core()->getConfigData('sales.invoice_settings.pdf_print_outs.logo')))) }}"/>
                    @else
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('themes/shop/logo_black.png'))) }}"/>
                    @endif
                </div>
                <div class="header-right {{ core()->getCurrentLocale()->direction === 'rtl' ? 'rtl' : '' }}">
                    <div class="invoice-title">
                        @lang('shop::app.customers.account.orders.invoice-pdf.invoice')
                    </div>
                </div>
            </div>

            <hr class="header-rule">

            <!-- ====== INFO BLOCK: Customer (left) + Invoice details (right) ====== -->
            <div class="info-row">
                <div class="info-col">
                    <div class="info-detail-row">
                        <div class="info-detail-label">
                            @lang('shop::app.customers.account.orders.invoice-pdf.bill-to')
                        </div>
                        @if ($invoice->order->billing_address)
                            <div class="info-value">
                                @if ($invoice->order->billing_address->company_name)
                                    <div>{{ $invoice->order->billing_address->company_name }}</div>
                                @endif
                                <div>{{ $invoice->order->billing_address->name }}</div>
                                <div>{{ $invoice->order->billing_address->address }}</div>
                                <div>{{ $invoice->order->billing_address->postcode . ' ' . $invoice->order->billing_address->city }}</div>
                                <div>{{ $invoice->order->billing_address->state . ', ' . core()->country_name($invoice->order->billing_address->country) }}</div>
                                <div>@lang('shop::app.customers.account.orders.invoice-pdf.contact'): {{ $invoice->order->billing_address->phone }}</div>
                            </div>
                        @endif
                    </div>

                    @if (core()->getConfigData('sales.shipping.origin.bank_details'))
                        <div class="info-detail-row">
                            <div class="info-detail-label">
                                @lang('shop::app.customers.account.orders.invoice-pdf.bank-details')
                            </div>
                            <div class="info-value">
                                {!! nl2br(core()->getConfigData('sales.shipping.origin.bank_details')) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="info-col info-col-right {{ core()->getCurrentLocale()->direction === 'rtl' ? 'rtl' : '' }}">
                    @if (core()->getConfigData('sales.invoice_settings.pdf_print_outs.invoice_id'))
                        <div class="info-detail-row">
                            <div class="info-detail-label">
                                @lang('shop::app.customers.account.orders.invoice-pdf.invoice-id')
                            </div>
                            <div class="info-detail-value">
                                #{{ $invoice->increment_id ?? $invoice->id }}
                            </div>
                        </div>
                    @endif

                    @if (core()->getConfigData('sales.invoice_settings.pdf_print_outs.order_id'))
                        <div class="info-detail-row">
                            <div class="info-detail-label">
                                @lang('shop::app.customers.account.orders.invoice-pdf.order-id')
                            </div>
                            <div class="info-detail-value">
                                #{{ $invoice->order->increment_id }}
                            </div>
                        </div>
                    @endif

                    <div class="info-detail-row">
                        <div class="info-detail-label">
                            @lang('shop::app.customers.account.orders.invoice-pdf.date')
                        </div>
                        <div class="info-detail-value">
                            {{ core()->formatDate($invoice->created_at, 'd-m-Y') }}
                        </div>
                    </div>

                    <div class="info-detail-row">
                        <div class="info-detail-label">
                            @lang('shop::app.customers.account.orders.invoice-pdf.order-date')
                        </div>
                        <div class="info-detail-value">
                            {{ core()->formatDate($invoice->order->created_at, 'd-m-Y') }}
                        </div>
                    </div>

                    @if ($invoice->hasPaymentTerm())
                        <div class="info-detail-row">
                            <div class="info-detail-label">
                                @lang('shop::app.customers.account.orders.invoice-pdf.payment-terms')
                            </div>
                            <div class="info-detail-value">
                                {{ $invoice->getFormattedPaymentTerm() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ====== SHIPPING ADDRESS ====== -->
            @if ($invoice->order->shipping_address)
                <hr class="section-rule">

                <div class="section-label">
                    @lang('shop::app.customers.account.orders.invoice-pdf.ship-to')
                </div>

                <div class="address-row">
                    <div class="address-col {{ core()->getCurrentLocale()->direction === 'rtl' ? 'rtl' : '' }}">
                        <div class="info-value">
                            @if ($invoice->order->shipping_address->company_name)
                                <div>{{ $invoice->order->shipping_address->company_name }}</div>
                            @endif
                            <div>{{ $invoice->order->shipping_address->name }}</div>
                            <div>{{ $invoice->order->shipping_address->address }}</div>
                            <div>{{ $invoice->order->shipping_address->postcode . ' ' . $invoice->order->shipping_address->city }}</div>
                            <div>{{ $invoice->order->shipping_address->state . ', ' . core()->country_name($invoice->order->shipping_address->country) }}</div>
                            <div>@lang('shop::app.customers.account.orders.invoice-pdf.contact'): {{ $invoice->order->shipping_address->phone }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ====== PAYMENT & SHIPPING METHODS ====== -->
            <hr class="section-rule">

            <div class="info-row">
                <div class="info-col">
                    <div class="info-detail-label">
                        @lang('shop::app.customers.account.orders.invoice-pdf.payment-method')
                    </div>
                    <div class="info-detail-value">
                        {{ core()->getConfigData('sales.payment_methods.' . $invoice->order->payment->method . '.title') }}

                        @php $additionalDetails = \Frooxi\Payment\Payment::getAdditionalDetails($invoice->order->payment->method); @endphp

                        @if (! empty($additionalDetails))
                            <div class="small-text">
                                {{ $additionalDetails['title'] }}: {{ $additionalDetails['value'] }}
                            </div>
                        @endif
                    </div>
                </div>

                @if ($invoice->order->shipping_address)
                    <div class="info-col info-col-right {{ core()->getCurrentLocale()->direction === 'rtl' ? 'rtl' : '' }}">
                        <div class="info-detail-label">
                            @lang('shop::app.customers.account.orders.invoice-pdf.shipping-method')
                        </div>
                        <div class="info-detail-value">
                            {{ $invoice->order->shipping_title }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- ====== LINE ITEMS TABLE ====== -->
            <hr class="section-rule">

            <table class="items {{ core()->getCurrentLocale()->direction }}">
                <thead>
                    <tr>
                        <th style="width: 45%;">
                            @lang('shop::app.customers.account.orders.invoice-pdf.product-name')
                        </th>
                        <th style="width: 20%;">
                            @lang('shop::app.customers.account.orders.invoice-pdf.price')
                        </th>
                        <th style="width: 15%;">
                            @lang('shop::app.customers.account.orders.invoice-pdf.qty')
                        </th>
                        <th style="width: 20%;">
                            @lang('shop::app.customers.account.orders.invoice-pdf.subtotal')
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>
                                {{ $item->name }}

                                @if (isset($item->additional['attributes']))
                                    <div class="item-attr">
                                        @foreach ($item->additional['attributes'] as $attribute)
                                            @if (
                                                ! isset($attribute['attribute_type'])
                                                || $attribute['attribute_type'] !== 'file'
                                            )
                                                {{ $attribute['attribute_name'] }}: {{ $attribute['option_label'] }}
                                                @if (! $loop->last)
                                                   ,
                                                @endif
                                            @else
                                                {{ $attribute['attribute_name'] }}:
                                                <a
                                                    href="{{ Storage::url($attribute['option_label']) }}"
                                                    download="{{ File::basename($attribute['option_label']) }}"
                                                >
                                                    {{ File::basename($attribute['option_label']) }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                    {!! $formatPdfPrice($item->price_incl_tax, $orderCurrencyCode) !!}
                                @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                                    {!! $formatPdfPrice($item->price_incl_tax, $orderCurrencyCode) !!}

                                    <div class="small-text">
                                        @lang('shop::app.customers.account.orders.invoice-pdf.excl-tax')
                                        {{ $formatPdfPrice($item->price, $orderCurrencyCode) }}
                                    </div>
                                @else
                                    {!! $formatPdfPrice($item->price, $orderCurrencyCode) !!}
                                @endif
                            </td>

                            <td>
                                {{ $item->qty }}
                            </td>

                            <td>
                                @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                    {!! $formatPdfPrice($item->total_incl_tax, $orderCurrencyCode) !!}
                                @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                    {!! $formatPdfPrice($item->total_incl_tax, $orderCurrencyCode) !!}

                                    <div class="small-text">
                                        @lang('shop::app.customers.account.orders.invoice-pdf.excl-tax')
                                        {{ $formatPdfPrice($item->total, $orderCurrencyCode) }}
                                    </div>
                                @else
                                    {!! $formatPdfPrice($item->total, $orderCurrencyCode) !!}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- ====== SUMMARY ====== -->
            <div class="summary-block">
                <table class="summary-table {{ core()->getCurrentLocale()->direction }}">
                    <tbody>
                        @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.subtotal')</td>
                                <td>{!! $formatPdfPrice($invoice->sub_total_incl_tax, $orderCurrencyCode) !!}</td>
                            </tr>
                        @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.subtotal-incl-tax')</td>
                                <td>{!! $formatPdfPrice($invoice->sub_total_incl_tax, $orderCurrencyCode) !!}</td>
                            </tr>

                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.subtotal-excl-tax')</td>
                                <td>{!! $formatPdfPrice($invoice->sub_total, $orderCurrencyCode) !!}</td>
                            </tr>
                        @else
                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.subtotal')</td>
                                <td>{!! $formatPdfPrice($invoice->sub_total, $orderCurrencyCode) !!}</td>
                            </tr>
                        @endif

                        @if (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'including_tax')
                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.shipping-handling')</td>
                                <td>{!! $formatPdfPrice($invoice->shipping_amount_incl_tax, $orderCurrencyCode) !!}</td>
                            </tr>
                        @elseif (core()->getConfigData('sales.taxes.sales.display_shipping_amount') == 'both')
                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.shipping-handling-incl-tax')</td>
                                <td>{!! $formatPdfPrice($invoice->shipping_amount_incl_tax, $orderCurrencyCode) !!}</td>
                            </tr>

                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.shipping-handling-excl-tax')</td>
                                <td>{!! $formatPdfPrice($invoice->shipping_amount, $orderCurrencyCode) !!}</td>
                            </tr>
                        @else
                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.shipping-handling')</td>
                                <td>{!! $formatPdfPrice($invoice->shipping_amount, $orderCurrencyCode) !!}</td>
                            </tr>
                        @endif

                        <tr>
                            <td>@lang('shop::app.customers.account.orders.invoice-pdf.tax')</td>
                            <td>{!! $formatPdfPrice($invoice->tax_amount, $orderCurrencyCode) !!}</td>
                        </tr>

                        @if ($invoice->discount_amount > 0)
                            <tr>
                                <td>@lang('shop::app.customers.account.orders.invoice-pdf.discount')</td>
                                <td>{!! $formatPdfPrice($invoice->discount_amount, $orderCurrencyCode) !!}</td>
                            </tr>
                        @endif

                        <tr class="grand-total">
                            <td><b>@lang('shop::app.customers.account.orders.invoice-pdf.grand-total')</b></td>
                            <td><b>{!! $formatPdfPrice($invoice->grand_total, $orderCurrencyCode) !!}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
