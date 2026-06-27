{!! view_render_event('frooxi.admin.catalog.product.edit.form.categories.before', ['product' => $product]) !!}

<!-- Panel -->
<div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
    <!-- Panel Header -->
    <p class="mb-4 flex justify-between text-lg font-semibold text-gray-900 dark:text-white">
        @lang('admin::app.catalog.products.edit.categories.title')
    </p>

    {!! view_render_event('frooxi.admin.catalog.product.edit.form.categories.controls.before', ['product' => $product]) !!}

    <!-- Panel Content -->
    <div class="mb-5 text-sm text-gray-600 dark:text-gray-300">

        <v-product-categories>
            <x-admin::shimmer.tree />
        </v-product-categories>

    </div>

    {!! view_render_event('frooxi.admin.catalog.product.edit.form.categories.controls.after', ['product' => $product]) !!}
</div>

{!! view_render_event('frooxi.admin.catalog.product.edit.form.categories.after', ['product' => $product]) !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-categories-template"
    >
        <div>
            <template v-if="isLoading">
                <x-admin::shimmer.tree />
            </template>

            <template v-else>
                <x-admin::tree.view
                    input-type="checkbox"
                    selection-type="individual"
                    name-field="categories"
                    id-field="id"
                    value-field="id"
                    ::items="categories"
                    :value="json_encode($product->categories->pluck('id'))"
                    :fallback-locale="config('app.fallback_locale')"
                />
            </template>
        </div>
    </script>

    <script type="module">
        app.component('v-product-categories', {
            template: '#v-product-categories-template',

            data() {
                return {
                    isLoading: true,

                    categories: [],
                }
            },

            mounted() {
                this.get();
            },

            methods: {
                get() {
                    axios.get("{{ route('admin.catalog.categories.tree') }}", { // Update this line
                            params: {
                                channel: "{{ $currentChannel->code }}",
                            }
                        })
                        .then(response => {
                            this.isLoading = false;

                            this.categories = response.data.data;
                        }).catch(error => {
                            console.log(error);
                        });
                }
            }
        });
    </script>
@endpushOnce
