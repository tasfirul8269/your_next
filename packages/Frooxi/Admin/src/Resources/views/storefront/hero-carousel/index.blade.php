<x-admin::layouts>
    <x-slot:title>
        Hero Carousel
    </x-slot>

    <v-hero-carousel>
        <div class="flex items-center justify-between">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                Hero Carousel
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
    </v-hero-carousel>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-hero-carousel-template">
            <div>
                <div class="flex items-center justify-between">
                    <p class="text-xl font-bold text-gray-800 dark:text-white">
                        Hero Carousel
                    </p>

                    <div
                        class="primary-button"
                        @click="selectedSlide = {}; $refs.slideModal.toggle()"
                    >
                        Add New Slide
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

                <!-- Slide List -->
                <div class="mt-5 grid gap-4">
                    <div
                        v-if="slides.length"
                        class="box-shadow relative overflow-hidden rounded bg-white dark:bg-gray-900"
                    >
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3">Order</th>
                                        <th class="px-6 py-3">Media</th>
                                        <th class="px-6 py-3">Type</th>
                                        <th class="px-6 py-3">Title/Link</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(slide, index) in slides"
                                        :key="slide.id"
                                        class="border-b bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800"
                                    >
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <i class="icon-drag cursor-move text-xl text-gray-400"></i>
                                                @{{ slide.sort_order }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="h-16 w-32 overflow-hidden rounded border dark:border-gray-700">
                                                <img
                                                    v-if="slide.type === 'image'"
                                                    :src="slide.media_url"
                                                    class="h-full w-full object-cover"
                                                />
                                                <video
                                                    v-else
                                                    :src="slide.media_url"
                                                    class="h-full w-full object-cover"
                                                    muted
                                                ></video>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                :class="slide.type === 'image' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'"
                                            >
                                                @{{ slide.type.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-gray-900 dark:text-white">@{{ slide.title || 'No Title' }}</span>
                                                <span class="text-xs text-gray-500">
                                                    @{{ slide.category_name ? 'Category: ' + slide.category_name : 'No Category' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <label class="relative inline-flex cursor-pointer items-center">
                                                <input
                                                    type="checkbox"
                                                    :checked="slide.status"
                                                    @change="toggleStatus(slide)"
                                                    class="peer sr-only"
                                                >
                                                <div class="peer h-5 w-9 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:peer-focus:ring-blue-800"></div>
                                            </label>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end gap-3 text-lg">
                                                <i
                                                    class="icon-edit cursor-pointer text-blue-600"
                                                    @click="editSlide(slide)"
                                                ></i>
                                                <i
                                                    class="icon-delete cursor-pointer text-red-600"
                                                    @click="deleteSlide(slide.id)"
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
                        <p class="text-base font-semibold text-gray-400">No slides found for this channel.</p>
                    </div>
                </div>

                <!-- Add/Edit Modal -->
                <x-admin::modal ref="slideModal">
                    <x-slot:header>
                        <p class="text-lg font-bold text-gray-800 dark:text-white">
                            @{{ selectedSlide.id ? 'Edit Slide' : 'Add New Slide' }}
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div class="grid gap-4">
                            <!-- Type -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-gray-800 dark:text-white required">Type</label>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" value="image" v-model="selectedSlide.type" class="w-4 h-4 text-blue-600">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Image</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" value="video" v-model="selectedSlide.type" class="w-4 h-4 text-blue-600">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">Video</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Title -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Title</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control
                                    type="text"
                                    name="title"
                                    v-model="selectedSlide.title"
                                    placeholder="Slide Title"
                                />
                            </x-admin::form.control-group>

                            <!-- Category Selection -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>Category</x-admin::form.control-group.label>
                                <select
                                    class="custom-select w-full cursor-pointer rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 outline-none transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                    v-model="selectedSlide.category_id"
                                    style="font-family: 'Courier New', monospace; line-height: 1.6;"
                                >
                                    <option value="" style="font-weight: 600;">— Select a category (optional) —</option>
                                    <template v-for="category in categories" :key="category.id">
                                        <option
                                            v-if="category.level === 0"
                                            :value="category.id"
                                            style="font-weight: 600;"
                                            v-text="'📁 ' + category.name"
                                        >
                                        </option>
                                        <option
                                            v-else-if="category.level === 1"
                                            :value="category.id"
                                            style="padding-left: 20px;"
                                            v-text="'  └─ ' + category.name"
                                        >
                                        </option>
                                        <option
                                            v-else
                                            :value="category.id"
                                            v-text="getCategoryIndent(category) + category.name"
                                        >
                                        </option>
                                    </template>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    When clicked, this banner will redirect to the selected category page.
                                </p>
                            </x-admin::form.control-group>

                            <!-- Media Upload -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-gray-800 dark:text-white" :class="!selectedSlide.id ? 'required' : ''">
                                    @{{ selectedSlide.type === 'video' ? 'Video File' : 'Image File' }}
                                </label>
                                <input
                                    type="file"
                                    @change="onFileChange"
                                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 outline-none transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                    :accept="selectedSlide.type === 'video' ? 'video/*' : 'image/*'"
                                />
                                <p class="text-xs text-gray-500">
                                    @{{ selectedSlide.type === 'video' ? 'Accepted formats: MP4, WebM. Max size: 50MB.' : 'Accepted formats: JPG, PNG, WebP.' }}
                                </p>
                            </div>

                            <!-- Preview -->
                            <div v-if="selectedSlide.id || previewUrl" class="mt-2">
                                <p class="text-xs font-medium text-gray-500 mb-2">Preview:</p>
                                <div class="h-40 w-full overflow-hidden rounded border dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
                                    <img
                                        v-if="selectedSlide.type === 'image'"
                                        :src="previewUrl || selectedSlide.media_url"
                                        class="h-full w-full object-contain"
                                    />
                                    <video
                                        v-else
                                        :src="previewUrl || selectedSlide.media_url"
                                        class="h-full w-full object-contain"
                                        controls
                                    ></video>
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot:footer>
                        <button
                            class="primary-button"
                            @click="saveSlide"
                            :disabled="isProcessing"
                        >
                            @{{ isProcessing ? 'Processing...' : 'Save Slide' }}
                        </button>
                    </x-slot>
                </x-admin::modal>
            </div>
        </script>

        <script type="module">
            app.component('v-hero-carousel', {
                template: '#v-hero-carousel-template',

                data() {
                    return {
                        slides: @json($slides),
                        channels: @json($channels),
                        channelId: @json($channelId),
                        categories: @json($categories),
                        selectedSlide: {
                            type: 'image',
                            category_id: ''
                        },
                        previewUrl: null,
                        selectedFile: null,
                        isProcessing: false,
                    }
                },

                methods: {
                    changeChannel() {
                        window.location.href = "{{ route('admin.storefront.hero_carousel.index') }}?channel_id=" + this.channelId;
                    },

                    getCategoryIndent(category) {
                        if (category.level === 0 || category.level === 1) {
                            return ''; // Handled in template
                        }
                        
                        // For level 2+, create proper tree indentation
                        let indent = '';
                        for (let i = 1; i < category.level; i++) {
                            indent += '     '; // 5 spaces for each level
                        }
                        return indent + '└─ ';
                    },

                    onFileChange(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.selectedFile = file;
                            this.previewUrl = URL.createObjectURL(file);
                        }
                    },

                    async saveSlide() {
                        if (!this.selectedSlide.id && !this.selectedFile) {
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Please select a file.' });
                            return;
                        }

                        this.isProcessing = true;
                        const formData = new FormData();
                        
                        if (this.selectedFile) {
                            formData.append('media_file', this.selectedFile);
                        }
                        
                        formData.append('type', this.selectedSlide.type || 'image');
                        formData.append('title', this.selectedSlide.title || '');
                        formData.append('category_id', this.selectedSlide.category_id || '');
                        formData.append('channel_id', this.channelId);

                        if (this.selectedSlide.id) {
                            formData.append('_method', 'PUT');
                        }

                        try {
                            const url = this.selectedSlide.id 
                                ? "{{ route('admin.storefront.hero_carousel.update', ':id') }}".replace(':id', this.selectedSlide.id)
                                : "{{ route('admin.storefront.hero_carousel.store') }}";

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

                    editSlide(slide) {
                        this.selectedSlide = { ...slide };
                        this.previewUrl = null;
                        this.selectedFile = null;
                        this.$refs.slideModal.toggle();
                    },

                    async deleteSlide(id) {
                        if (!confirm('Are you sure you want to delete this slide?')) return;

                        try {
                            const response = await axios.delete("{{ route('admin.storefront.hero_carousel.destroy', ':id') }}".replace(':id', id));
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            this.slides = this.slides.filter(s => s.id !== id);
                        } catch (error) {
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Failed to delete slide.' });
                        }
                    },

                    async toggleStatus(slide) {
                        slide.status = !slide.status;
                        try {
                             await axios.put("{{ route('admin.storefront.hero_carousel.update', ':id') }}".replace(':id', slide.id), {
                                status: slide.status
                            });
                            this.$emitter.emit('add-flash', { type: 'success', message: 'Status updated.' });
                        } catch (error) {
                            slide.status = !slide.status;
                            this.$emitter.emit('add-flash', { type: 'error', message: 'Failed to update status.' });
                        }
                    }
                }
            });
        </script>
    @endpushOnce
</x-admin::layouts>
