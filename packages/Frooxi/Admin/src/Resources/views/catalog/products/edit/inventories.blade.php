{!! view_render_event('frooxi.admin.catalog.product.edit.form.inventories.controls.before', ['product' => $product]) !!}

@php
    $inventorySource = app(\Frooxi\Inventory\Repositories\InventorySourceRepository::class)->first();
    $inventorySourceId = $inventorySource?->id ?? 1;
    $currentQty = $product->inventories
        ->where('inventory_source_id', $inventorySourceId)
        ->pluck('qty')
        ->first() ?? 0;
@endphp

<!-- Manage Stock Toggle -->
<x-admin::form.control-group class="mb-4">
    <x-admin::form.control-group.label>
        Manage Stock
    </x-admin::form.control-group.label>

    <x-admin::form.control-group.control
        type="switch"
        name="manage_stock"
        value="1"
        label="Manage Stock"
        :checked="(boolean) old('manage_stock', $product->manage_stock)"
        id="manage_stock"
    />

    <p class="mt-1 text-xs text-gray-500">
        Enable to track and manage stock quantity
    </p>
</x-admin::form.control-group>

<!-- Stock Quantity (shown only when Manage Stock is enabled) -->
<v-inventories>
    <div class="mt-3">
        <x-admin::form.control-group>
            <x-admin::form.control-group.label>
                Stock Quantity
            </x-admin::form.control-group.label>

            <x-admin::form.control-group.control
                type="text"
                name="inventories[{{ $inventorySourceId }}]"
                rules="nullable|numeric|min:0"
                :value="old('inventories.{{ $inventorySourceId }}', $currentQty)"
                label="Stock Quantity"
                placeholder="Enter stock quantity"
            />

            <x-admin::form.control-group.error control-name="inventories[{{ $inventorySourceId }}]" />

            <p class="mt-1 text-xs text-gray-500">
                Stock will automatically reduce after each purchase
            </p>
        </x-admin::form.control-group>
    </div>
</v-inventories>

{!! view_render_event('frooxi.admin.catalog.product.edit.form.inventories.controls.after', ['product' => $product]) !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-inventories-template"
    >
        <div v-show="manageStock">
            <slot></slot>
        </div>
    </script>

    <script type="module">
        app.component('v-inventories', {
            template: '#v-inventories-template',

            data() {
                return {
                    manageStock: {{ (boolean) $product->manage_stock ? 'true' : 'false' }},
                }
            },

            mounted() {
                let self = this;
                document.getElementById('manage_stock').addEventListener('change', function(e) {
                    self.manageStock = e.target.checked;
                });
            }
        });
    </script>
@endpushOnce
