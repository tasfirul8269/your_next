@if (isset($prices['final']) && $prices['final']['price'] < $prices['regular']['price'])
    @php
        $defaultVariant = $product->getTypeInstance()->getDefaultVariant();
        $discountPercentage = $defaultVariant ? floatval($defaultVariant->discount_percentage) : 0;
    @endphp
    <p
        class="regular-price font-medium text-zinc-500 line-through max-sm:leading-4"
        aria-label="{{ $prices['regular']['formatted_price'] }}"
    >
        {{ $prices['regular']['formatted_price'] }}
    </p>

    <div class="flex items-center gap-2">
        <p class="final-price font-semibold max-sm:leading-4">
            {{ $prices['final']['formatted_price'] }}
        </p>
        <span class="inline-flex rounded-md bg-red-100 px-1.5 py-0.5 text-xs font-semibold text-red-600">
            {{ $discountPercentage }}% OFF
        </span>
    </div>
@else
    <p class="regular-price text-lg font-semibold text-gray-500 line-through" style="display: none;"></p>
    <p class="final-price font-semibold max-sm:leading-4">
        {{ $prices['regular']['formatted_price'] }}
    </p>
@endif