<!-- Flash Sale Discount Percentage Field -->
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
            Enter discount (1-99%). Leave 0 to disable flash sale.
        </p>
    </x-admin::form.control-group>
@endif
