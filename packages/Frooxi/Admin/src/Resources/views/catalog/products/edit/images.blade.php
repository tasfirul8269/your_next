{!! view_render_event('frooxi.admin.catalog.product.edit.form.images.before', ['product' => $product]) !!}

<div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
    <!-- Panel Header -->
    <div class="mb-4 flex justify-between gap-5">
        <div class="flex flex-col gap-2">
            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                @lang('admin::app.catalog.products.edit.images.title')
            </p>

            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                @lang('admin::app.catalog.products.edit.images.info')
            </p>
        </div>
    </div>

    <!-- Image Upload Area -->
    <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-6 text-center hover:border-[#D4A84B] transition-colors dark:border-gray-700 dark:bg-gray-800/50">
        <!-- Image Blade Component -->
        <x-admin::media.images
            name="images[files]"
            allow-multiple="true"
            show-placeholders="true"
            :uploaded-images="$product->images"
        />
    </div>

    <x-admin::form.control-group.error control-name='images.files[0]' />
</div>

{!! view_render_event('frooxi.admin.catalog.product.edit.form.images.after', ['product' => $product]) !!}