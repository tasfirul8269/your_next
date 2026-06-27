<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.catalog.categories.index.title')
    </x-slot>

    <div class="p-6 space-y-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <div>
                <h1 class="font-serif text-2xl font-bold text-gray-900">
                    @lang('admin::app.catalog.categories.index.title')
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage your store categories
                </p>
            </div>

            <div class="flex items-center gap-x-2.5">
                {!! view_render_event('frooxi.admin.catalog.categories.index.create-button.before') !!}

                @if (bouncer()->hasPermission('catalog.categories.create'))
                    <a href="{{ route('admin.catalog.categories.create') }}">
                        <button
                            type="button"
                            class="primary-button"
                        >
                            @lang('admin::app.catalog.categories.index.add-btn')
                        </button>
                    </a>
                @endif

                {!! view_render_event('frooxi.admin.catalog.categories.index.create-button.after') !!}
            </div>
        </div>

        <!-- Category Tree View -->
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <v-category-tree></v-category-tree>
        </div>

    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-category-tree-template">
            <div>
                <!-- Loading State -->
                <div v-if="isLoading" class="flex items-center justify-center py-12">
                    <p style="font-size:13px;color:#9ca3af;">Loading categories...</p>
                </div>

                <!-- Tree View -->
                <div v-else class="space-y-4">
                    <template v-for="category in categories" :key="category.id">
                        <v-category-item 
                            :category="category" 
                            :level="0"
                            @refresh="fetchCategories"
                        ></v-category-item>
                    </template>

                    <!-- Empty State -->
                    <div v-if="categories.length === 0" class="text-center py-12">
                        <p style="font-size:14px;color:#9ca3af;">No categories found</p>
                    </div>
                </div>
            </div>
        </script>

        <script type="text/x-template" id="v-category-item-template">
            <div>
                <!-- Category Item -->
                <div 
                    class="flex items-center justify-between p-4 mb-2 cursor-pointer transition-all rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50"
                    :style="{ marginLeft: (level * 32) + 'px' }"
                    @click="toggleExpand"
                >
                    <div class="flex items-center gap-3">
                        <!-- Expand Icon -->
                        <span 
                            v-if="category.children && category.children.length > 0"
                            class="text-xs text-gray-400 transition-transform"
                            :class="{ 'rotate-90': isExpanded }"
                        >
                            ▶
                        </span>
                        <span v-else class="inline-block w-3"></span>

                        <!-- Category Info -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">
                                @{{ category.name }}
                            </h3>
                            <p class="text-xs text-gray-400">
                                @{{ category.slug }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Status Badge -->
                        <span 
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="category.status ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                        >
                            @{{ category.status ? 'Active' : 'Inactive' }}
                        </span>

                        <!-- Actions -->
                        <a
                            :href="'/admin/catalog/categories/edit/' + category.id"
                            class="rounded-md p-2 cursor-pointer transition-colors hover:bg-gray-100"
                        >
                            <span class="icon-edit text-lg text-gray-500"></span>
                        </a>

                        @if (bouncer()->hasPermission('catalog.categories.delete'))
                            <span
                                @click.stop="deleteCategory(category)"
                                class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-red-50 max-sm:place-self-center"
                                title="Delete"
                            >
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Children -->
                <div v-if="isExpanded && category.children && category.children.length > 0" class="mt-2">
                    <template v-for="child in category.children" :key="child.id">
                        <v-category-item 
                            :category="child" 
                            :level="level + 1"
                            @refresh="$emit('refresh')"
                        ></v-category-item>
                    </template>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-category-tree', {
                template: '#v-category-tree-template',

                data() {
                    return {
                        isLoading: true,
                        categories: []
                    };
                },

                mounted() {
                    this.fetchCategories();
                },

                methods: {
                    fetchCategories() {
                        this.isLoading = true;
                        
                        this.$axios.get("{{ route('admin.catalog.categories.tree') }}")
                            .then(response => {
                                this.categories = response.data.data || [];
                                this.isLoading = false;
                            })
                            .catch(error => {
                                console.error(error);
                                this.isLoading = false;
                            });
                    }
                }
            });

            app.component('v-category-item', {
                template: '#v-category-item-template',
                
                props: ['category', 'level'],

                data() {
                    return {
                        isExpanded: this.level === 0
                    };
                },

                methods: {
                    toggleExpand() {
                        if (this.category.children && this.category.children.length > 0) {
                            this.isExpanded = !this.isExpanded;
                        }
                    },

                    deleteCategory(category) {
                        if (category.children && category.children.length > 0) {
                            this.$emitter.emit('add-flash', { type: 'warning', message: 'Cannot delete a category that has subcategories. Please delete subcategories first.' });

                            return;
                        }

                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                this.$axios.delete("{{ url('/api/v1/admin/categories') }}/" + category.id)
                                    .then(response => {
                                        this.$emitter.emit('add-flash', { type: 'success', message: 'Category deleted successfully.' });

                                        this.$emit('refresh');
                                    })
                                    .catch(error => {
                                        this.$emitter.emit('add-flash', { type: 'error', message: error.response?.data?.message || 'Failed to delete category.' });
                                    });
                            }
                        });
                    }
                }
            });
        </script>
    @endPushOnce

</x-admin::layouts>
