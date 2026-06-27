@if ($product->type != 'configurable')
<!-- Simplified Price Section: Base Price and Flash Sale Discount -->
<div class="grid gap-3">
    <!-- Base Price -->
    <x-admin::form.control-group>
        <x-admin::form.control-group.label class="required">
            Base Price
        </x-admin::form.control-group.label>

        <x-admin::form.control-group.control
            type="price"
            name="price"
            rules="required"
            :value="old('price') ?: (isset($product->price) && $product->price !== null ? rtrim(rtrim(number_format((float) $product->price, 4, '.', ''), '0'), '.') : '')"
            label="Base Price"
        >
            <x-slot:currency>
                {{ core()->currencySymbol(core()->getBaseCurrencyCode()) }}
            </x-slot>
        </x-admin::form.control-group.control>

        <x-admin::form.control-group.error control-name="price" />
    </x-admin::form.control-group>

    <!-- Discount Percentage -->
    <x-admin::form.control-group>
        <x-admin::form.control-group.label>
            Discount Percentage (%)
        </x-admin::form.control-group.label>

        <x-admin::form.control-group.control
            type="number"
            name="discount_percentage"
            rules="min:0|max:100"
            :value="old('discount_percentage') ?: ($product->discount_percentage ?? '')"
            placeholder="e.g. 10"
            label="Discount Percentage"
        />

        <x-admin::form.control-group.error control-name="discount_percentage" />
        
        <p class="mt-1 text-xs text-gray-500">
            Enter a discount percentage to apply to the base price.
        </p>
    </x-admin::form.control-group>

    <!-- Flash Sale Discount -->
    @if (request()->get('flash_sale') || $product->getAttributeValue('flash_sale_discount'))
        <x-admin::form.control-group>
            <x-admin::form.control-group.label :class="request()->get('flash_sale') ? 'required' : ''">
                Discount Percentage (%)
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="number"
                name="flash_sale_discount"
                rules="integer|min:0|max:99"
                :value="old('flash_sale_discount') ?: ($product->getAttributeValue('flash_sale_discount') ?? '')"
                placeholder="e.g. 30"
                min="0"
                max="99"
                label="Discount Percentage"
            />

            <x-admin::form.control-group.error control-name="flash_sale_discount" />
            
            <p class="mt-1 text-xs text-gray-500">
                Enter discount (1-99%). Leave 0 to disable flash sale. The sale price will be calculated automatically.
            </p>
        </x-admin::form.control-group>
    @endif
</div>
@endif
