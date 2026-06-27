<x-admin::layouts>
    <x-slot:title>
        Flash Sale
    </x-slot>

    <v-flash-sale>
        <div class="flex items-center justify-between">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                Flash Sale
            </p>

            <div class="shimmer h-[40px] w-[140px] rounded-md"></div>
        </div>

        <div class="mt-7 flex items-center justify-between border-b pb-4 dark:border-gray-800">
            <div class="shimmer h-[38px] w-[200px] rounded-md"></div>
        </div>

        <div class="mt-5 grid gap-4">
            <div class="shimmer h-[100px] w-full rounded-md"></div>
            <div class="shimmer h-[100px] w-full rounded-md"></div>
            <div class="shimmer h-[100px] w-full rounded-md"></div>
        </div>
    </v-flash-sale>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-flash-sale-template">
            <div>
                <div class="flex items-center justify-between">
                    <p class="text-xl font-bold text-gray-800 dark:text-white">
                        Flash Sale
                    </p>

                    <div
                        class="primary-button"
                        @click="selectedItem = { product_id: '' }; $refs.itemModal.toggle()"
                    >
                        Add New Item
                    </div>
                </div>

                <div class="mt-7 flex items-center justify-between border-b pb-4 dark:border-gray-800">
                    <div class="flex items-center gap-x-2.5">
                        <select
                            class="custom-select h-[40px] w-[200px] cursor-pointer rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-normal text-gray-600 outline-none transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            v-model="channelId"
                            @change="changeChannel"
                        >
                            <option
                                v-for="channel in channels"
                                :value="channel.id"
                            >
                                @{{ channel.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Items List -->
                <div class="mt-5 grid gap-4">
                    <div
                        v-if="items.length"
                        class="box-shadow relative overflow-hidden rounded bg-white dark:bg-gray-900"
                    >
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3">Order</th>
                                        <th class="px-6 py-3">Image</th>
                                        <th class="px-6 py-3">Title/Subtitle</th>
                                        <th class="px-6 py-3">Product/Link</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, index) in items"
                                        :key="item.id"
                                        class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800"
                                    >
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <i class="icon-drag cursor-move text-xl text-gray-400"></i>
                                                @{{ item.sort_order }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="h-20 w-[45px] overflow-hidden rounded border dark:border-gray-700">
                                                <img
                                                    v-if="item.image_url"
                                                    :src="item.image_url"
                                                    class="h-full w-full object-cover"
                                                />
                                                <div v-else class="h-full w-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-2xl">⚡</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900 dark:text-white">@{{ item.title || 'No Title' }}</span>
                                                <span class="text-xs text-gray-500">@{{ item.subtitle || 'No Subtitle' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900 dark:text-white" v-if="item.product_name">
                                                    @{{ item.product_name }}
                                                </span>
                                                <span class="text-xs text-gray-500" v-else-if="item.link">
                                                    Link: @{{ item.link }}
                                                </span>
                                                <span class="text-xs text-gray-500" v-else>
                                                    No Product/Link
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <label class="relative inline-flex cursor-pointer items-center">
                                                <input
                                                    type="checkbox"
                                                    :checked="item.status"
                                                    @change="toggleStatus(item)"
                                                    class="peer sr-only"
                                                >
                                                <div class="peer h-5 w-9 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-blue-800"></div>
                                            </label>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-3 text-lg">
                                                <i
                                                    class="icon-edit cursor-pointer text-blue-600"
                                                    @click="editItem(item)"
                                                ></i>
                                                <i
                                                    class="icon-delete cursor-pointer text-red-600"
                                                    @click="deleteItem(item.id)"
                                                ></i>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div
                        v-else
                        class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10"
                    >
                        <img
                            class="h-[120px] w-[120px] p-2 dark:mix-blend-exclusion dark:invert"
                            src="{{ frooxi_asset('images/empty-placeholders/default.svg') }}"
                        >
                        <p class="text-base font-semibold text-gray-400">No flash sale items found for this channel.</p>
                    </div>
                </div>

                <!-- Add/Edit Modal -->
                <x-admin::modal ref="itemModal">
                    <x-slot:header>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">
                            @{{ selectedItem.id ? 'Edit Flash Sale Item' : 'Add New Flash Sale Item' }}
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div class="grid gap-4">
                            <!-- Title -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">Title</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="title"
                                    v-model="selectedItem.title"
                                    placeholder="Flash Sale Title"
                                />
                            </x-admin::form.control-group>

                            <!-- Subtitle -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Subtitle</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="subtitle"
                                    v-model="selectedItem.subtitle"
                                    placeholder="Optional subtitle"
                                />
                            </x-admin::form.control-group>

                            <!-- Product Selection -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Product (Optional)</x-admin::form.control-group.label>
                                <select
                                    class="custom-select w-full cursor-pointer rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 outline-none transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                    v-model="selectedItem.product_id"
                                >
                                    <option value="">— Select a product (optional) —</option>
                                    <option
                                        v-for="product in products"
                                        :key="product.id"
                                        :value="product.id"
                                    >
                                        @{{ product.name }} (SKU: @{{ product.sku }})
                                    </option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    When clicked, this card will redirect to the selected product page.
                                </p>
                            </x-admin::form.control-group>

                            <!-- Custom Link (if no product) -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Custom Link (Optional)</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="link"
                                    v-model="selectedItem.link"
                                    placeholder="https://example.com"
                                />
                                <p class="text-xs text-gray-500 mt-1">
                                    Used only if no product is selected.
                                </p>
                            </x-admin::form.control-group>

                            <!-- Image Upload -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-gray-800 dark:text-white" :class="!selectedItem.id ? 'required' : ''">
                                    Image (9:16 ratio recommended)
                                </label>
                                <input
                                    type="file"
                                    @change="onFileChange"
                                    accept="image/*"
                                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 outline-none transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                />
                                <p class="text-xs text-gray-500">
                                    Accepted formats: JPG, PNG, WebP. Max size: 10MB. Recommended ratio: 9:16
                                </p>
                            </div>

                            <!-- Preview -->
                            <div v-if="selectedItem.id || previewUrl" class="mt-2">
                                <p class="text-xs font-medium text-gray-500 mb-2">Preview:</p>
                                <div class="h-48 w-[108px] mx-auto overflow-hidden rounded border dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
                                    <img
                                        :src="previewUrl || selectedItem.image_url"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot:footer>
                        <button
                            class="primary-button"
                            @click="saveItem"
                            :disabled="isProcessing"
                        >
                            @{{ isProcessing ? 'Processing...' : 'Save Item' }}
                        </button>
                    </x-slot>
                </x-admin::modal>
            </div>
        </script>

        <script type="module">
            app.component('v-flash-sale', {
                template: '#v-flash-sale-template',

                data() {
                    return {
                        items: @json($items),
                        channels: @json($channels),
                        channelId: @json($channelId),
                        products: @json($products),
                        selectedItem: {
                            product_id: ''
                        },
                        previewUrl: null,
                        selectedFile: null,
                        isProcessing: false,
                    }
                },

                methods: {
                    changeChannel() {
                        window.location.href = "{{ route('admin.storefront.flash_sale.index') }}?channel_id=" + this.channelId;
                    },

                    onFileChange(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.selectedFile = file;
                            this.previewUrl = URL.createObjectURL(file);
                        }
                    },

                    async saveItem() {
                        if (!this.selectedItem.id && !this.selectedFile) {
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Please select an image.' });
                            return;
                        }

                        if (!this.selectedItem.title) {
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Title is required.' });
                            return;
                        }

                        this.isProcessing = true;
                        const formData = new FormData();
                        
                        if (this.selectedFile) {
                            formData.append('image_file', this.selectedFile);
                        }
                        
                        formData.append('title', this.selectedItem.title || '');
                        formData.append('subtitle', this.selectedItem.subtitle || '');
                        formData.append('link', this.selectedItem.link || '');
                        if (this.selectedItem.product_id) {
                            formData.append('product_id', this.selectedItem.product_id);
                        }
                        formData.append('channel_id', this.channelId);

                        if (this.selectedItem.id) {
                            formData.append('_method', 'PUT');
                        }

                        try {
                            const url = this.selectedItem.id 
                                ? "{{ route('admin.storefront.flash_sale.update', ':id') }}".replace(':id', this.selectedItem.id)
                                : "{{ route('admin.storefront.flash_sale.store') }}";

                            const response = await axios.post(url, formData, {
                                headers: { 'Content-Type': 'multipart/form-data' }
                            });

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            
                            // Simple reload to refresh the list
                            window.location.reload();
                        } catch (error) {
                            const message = error.response?.data?.message || 'Something went wrong';
                            this.$emitter.emit('add-flash', { type: 'error', message });
                        } finally {
                            this.isProcessing = false;
                        }
                    },

                    editItem(item) {
                        this.selectedItem = { ...item };
                        this.previewUrl = null;
                        this.selectedFile = null;
                        this.$refs.itemModal.toggle();
                    },

                    async deleteItem(id) {
                        if (!confirm('Are you sure you want to delete this flash sale item?')) return;

                        try {
                            const response = await axios.delete("{{ route('admin.storefront.flash_sale.destroy', ':id') }}".replace(':id', id));
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            this.items = this.items.filter(i => i.id !== id);
                        } catch (error) {
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Failed to delete item.' });
                        }
                    },

                    async toggleStatus(item) {
                        item.status = !item.status;
                        try {
                             await axios.put("{{ route('admin.storefront.flash_sale.update', ':id') }}".replace(':id', item.id), {
                                status: item.status
                            });
                            this.$emitter.emit('add-flash', { type: 'success', message: 'Status updated.' });
                        } catch (error) {
                            item.status = !item.status;
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Failed to update status.' });
                        }
                    }
                }
            });
        </script>
    @endpushOnce
</x-admin::layouts>
