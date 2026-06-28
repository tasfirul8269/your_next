<x-admin::layouts>
    <x-slot:title>
        Flash Sale Products
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="font-serif text-2xl font-bold text-gray-900 dark:text-white">
            Flash Sale Products
        </p>

        <div class="flex items-center gap-x-2.5">
            @if (bouncer()->hasPermission('catalog.products.create'))
                <a
                    href="{{ route('admin.storefront.flash_sale.create') }}"
                    class="primary-button"
                >
                    Create Flash Sale Product
                </a>
            @endif
        </div>
    </div>

    <!-- Datagrid -->
    <x-admin::datagrid :src="route('admin.storefront.flash_sale.index')" />

</x-admin::layouts>
