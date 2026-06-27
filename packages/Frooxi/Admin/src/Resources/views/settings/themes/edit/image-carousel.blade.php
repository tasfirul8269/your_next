<v-image-carousel :errors="errors">
    <x-admin::shimmer.settings.themes.image-carousel />
</v-image-carousel>

<!-- Image Carousel Vue Component -->
@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-image-carousel-template"
    >
        <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                <div class="flex items-center justify-between gap-x-2.5">
                    <div class="flex flex-col gap-1">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.settings.themes.edit.slider')
                        </p>
                        
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-300">
                            @lang('admin::app.settings.themes.edit.slider-description')
                        </p>
                    </div>

                    <!-- Add Slider Button -->
                    <div
                        class="secondary-button"
                        @click="create"
                    >
                        @lang('admin::app.settings.themes.edit.slider-add-btn')
                    </div>
                </div>

                <template v-for="(deletedSlider, index) in deletedSliders">
                    <input
                        type="hidden"
                        :name="'{{ $currentLocale->code }}[deleted_sliders]['+ index +'][image]'"
                        :value="deletedSlider.image"
                    />
                </template>

                <div
                    class="grid pt-4"
                    v-if="sliders.images.length"
                    v-for="(image, index) in sliders.images"
                >
                    <!-- Hidden Input -->
                    <input
                        type="file"
                        class="hidden"
                        :name="'{{ $currentLocale->code }}[options]['+ index +'][image]'"
                        :ref="'imageInput_' + index"
                    />

                    <input
                        type="hidden"
                        :name="'{{ $currentLocale->code }}[options]['+ index +'][title]'"
                        :value="image.title"
                    />

                    <input
                        type="hidden"
                        :name="'{{ $currentLocale->code }}[options]['+ index +'][link]'"
                        :value="image.link"
                    />

                    <input
                        type="hidden"
                        :name="'{{ $currentLocale->code }}[options]['+ index +'][image]'"
                        :value="image.image"
                    />

                    <input
                        type="hidden"
                        :name="'{{ $currentLocale->code }}[options]['+ index +'][type]'"
                        :value="image.type || 'image'"
                    />
                
                    <!-- Details -->
                    <div 
                        class="flex cursor-pointer justify-between gap-2.5 py-5"
                        :class="{
                            'border-b border-slate-300 dark:border-gray-800': index < sliders.images.length - 1
                        }"
                    >
                        <div class="flex gap-2.5">
                            <div class="grid place-content-start gap-1.5">
                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.settings.themes.edit.image-title'): 

                                    <span class="text-gray-600 transition-all dark:text-gray-300">
                                        @{{ image.title }}
                                    </span>
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.settings.themes.edit.link'): 

                                    <span class="text-gray-600 transition-all dark:text-gray-300">
                                        @{{ image.link }}
                                    </span>
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    Media Type: 

                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="{
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': (image.type || 'image') === 'image',
                                            'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200': image.type === 'video'
                                        }"
                                    >
                                        @{{ (image.type || 'image').charAt(0).toUpperCase() + (image.type || 'image').slice(1) }}
                                    </span>
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.settings.themes.edit.image'): 

                                    <span class="text-gray-600 transition-all dark:text-gray-300">
                                        <a
                                            :href="'{{ config('app.url') }}/' + image.image"
                                            :ref="'image_' + index"
                                            target="_blank"
                                            class="text-blue-600 transition-all hover:underline ltr:ml-2 rtl:mr-2"
                                        >
                                            <span :ref="'imageName_' + index">
                                                @{{ image.image }}
                                            </span>
                                        </a>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="grid place-content-start gap-1 text-right">
                            <div class="flex items-center gap-x-5">
                                <p
                                    class="cursor-pointer text-blue-600 transition-all hover:underline"
                                    @click="edit(image, index)"
                                >
                                    @lang('admin::app.settings.themes.edit.edit')
                                </p>

                                <p
                                    class="cursor-pointer text-red-600 transition-all hover:underline"
                                    @click="remove(index)"
                                >
                                    @lang('admin::app.settings.themes.edit.delete')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty Page -->
                <div
                    class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10"
                    v-else
                >
                    <img
                        class="h-[120px] w-[120px] p-2 dark:mix-blend-exclusion dark:invert"
                        src="{{ frooxi_asset('images/empty-placeholders/default.svg') }}"
                        alt="@lang('admin::app.settings.themes.edit.slider')"
                    >

                    <div class="flex flex-col items-center gap-1.5">
                        <p class="text-base font-semibold text-gray-400">
                            @lang('admin::app.settings.themes.edit.slider-add-btn')
                        </p>
                        
                        <p class="text-gray-400">
                            @lang('admin::app.settings.themes.edit.slider-description')
                        </p>
                    </div>
                </div>
            </div>

            <x-admin::form v-slot="{ errors, handleSubmit }" as="div">
                <form
                    @submit.prevent="handleSubmit($event, saveSliderImage)"
                    enctype="multipart/form-data"
                    ref="createSliderForm"
                >
                    <x-admin::modal ref="addSliderModal">
                        <!-- Modal Header -->
                        <x-slot:header>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">
                                <template v-if="! isUpdating">
                                    @lang('admin::app.settings.themes.edit.slider-add-btn')
                                </template>

                                <template v-else>
                                    @lang('admin::app.settings.themes.edit.update-slider')
                                </template>
                            </p>
                        </x-slot>

                        <!-- Modal Content -->
                        <x-slot:content>
                            <!-- Media Type Selector -->
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    Media Type
                                </x-admin::form.control-group.label>

                                <div class="flex items-center gap-4">
                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input
                                            type="radio"
                                            name="media_type"
                                            value="image"
                                            v-model="selectedSlider.type"
                                            class="text-blue-600 focus:ring-blue-500"
                                        />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Image</span>
                                    </label>

                                    <label class="flex cursor-pointer items-center gap-2">
                                        <input
                                            type="radio"
                                            name="media_type"
                                            value="video"
                                            v-model="selectedSlider.type"
                                            class="text-blue-600 focus:ring-blue-500"
                                        />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Video</span>
                                    </label>
                                </div>
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.themes.edit.image-title')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="{{ $currentLocale->code }}[title]"
                                    rules="required"
                                    v-model="selectedSlider.title"
                                    :placeholder="trans('admin::app.settings.themes.edit.image-title')"
                                    :label="trans('admin::app.settings.themes.edit.image-title')"
                                />

                                <x-admin::form.control-group.error control-name="{{ $currentLocale->code }}[title]" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label>
                                    @lang('admin::app.settings.themes.edit.link')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control
                                    type="text"
                                    name="{{ $currentLocale->code }}[link]"
                                    v-model="selectedSlider.link"
                                    :placeholder="trans('admin::app.settings.themes.edit.link')"
                                />
                            </x-admin::form.control-group>

                            <!-- Image Upload (when type is image) -->
                            <x-admin::form.control-group v-if="selectedSlider.type !== 'video'">
                                <x-admin::form.control-group.label class="required">
                                    @lang('admin::app.settings.themes.edit.slider-image')
                                </x-admin::form.control-group.label>

                                <div class="hidden">
                                    <x-admin::media.images
                                        ::key="'slider_image_hidden_' + mediaComponentKey"
                                        name="slider_image"
                                        ::uploaded-images='selectedSliderMediaImages'
                                    />
                                </div>

                                <v-media-images
                                    :key="'slider_image_' + mediaComponentKey"
                                    name="slider_image"
                                    :uploaded-images='selectedSliderMediaImages'
                                >
                                </v-media-images>

                                <x-admin::form.control-group.error control-name="slider_image" />
                            </x-admin::form.control-group>

                            <!-- Video Upload (when type is video) -->
                            <x-admin::form.control-group v-if="selectedSlider.type === 'video'">
                                <x-admin::form.control-group.label class="required">
                                    Video File
                                </x-admin::form.control-group.label>

                                <div class="flex flex-col gap-2">
                                    <input
                                        type="file"
                                        name="slider_video"
                                        ref="videoFileInput"
                                        accept="video/mp4,video/webm"
                                        class="w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                                        @change="onVideoFileSelected"
                                    />

                                    <!-- Existing video preview -->
                                    <div
                                        v-if="selectedSlider.image && isUpdating && selectedSlider.type === 'video'"
                                        class="mt-2"
                                    >
                                        <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Current video:</p>
                                        <video
                                            :src="'{{ config('app.url') }}/' + selectedSlider.image"
                                            class="h-32 w-auto rounded border dark:border-gray-700"
                                            muted
                                            playsinline
                                        ></video>
                                    </div>
                                </div>

                                <x-admin::form.control-group.error control-name="slider_video" />
                            </x-admin::form.control-group>

                            <p class="text-xs text-gray-600 dark:text-gray-300">
                                <template v-if="selectedSlider.type !== 'video'">
                                    @lang('admin::app.settings.themes.edit.image-size')
                                </template>
                                <template v-else>
                                    Accepted formats: MP4, WebM. Max recommended size: 50MB.
                                </template>
                            </p>
                        </x-slot>

                        <!-- Modal Footer -->
                        <x-slot:footer>
                            <!-- Save Button -->
                            <button
                                type="button"
                                class="primary-button justify-center"
                                @click="handleSubmit($event, saveSliderImage)"
                            >
                                @lang('admin::app.settings.themes.edit.save-btn')
                            </button>
                        </x-slot>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </div>
    </script>

    <script type="module">
        app.component('v-image-carousel', {
            template: '#v-image-carousel-template',

            props: ['errors'],

            data() {
                return {
                    sliders: @json($theme->translate($currentLocale->code)['options'] ?? null),

                    deletedSliders: [],

                    selectedSlider: {
                        type: 'image',
                    },

                    selectedSliderMediaImages: [],

                    selectedSliderOriginalImage: null,

                    selectedVideoFile: null,

                    mediaComponentKey: 0,

                    selectedSliderIndex: null,

                    isUpdating: false,
                };
            },
            
            created() {
                if (! this.sliders || ! this.sliders.images) {
                    this.sliders = { images: [] };
                }
            },

            methods: {
                saveSliderImage(_, { resetForm, setErrors }) {
                    const formData = new FormData(this.$refs.createSliderForm);
                    const mediaType = this.selectedSlider.type || 'image';

                    try {
                        const sliderData = {
                            title: formData.get("{{ $currentLocale->code }}[title]"),
                            link: formData.get("{{ $currentLocale->code }}[link]"),
                            type: mediaType,
                        };

                        if (mediaType === 'video') {
                            // Video flow
                            const videoFile = this.selectedVideoFile;
                            const hasVideoFile = videoFile instanceof File && videoFile.name !== '';

                            if (! hasVideoFile && ! (this.isUpdating && this.selectedSlider.image)) {
                                throw new Error("Please upload a video file.");
                            }

                            const sliderIndex = this.upsertSlider(sliderData);

                            if (hasVideoFile) {
                                this.setFile(videoFile, sliderIndex);
                                this.markSliderImageForDeletion();
                            }
                        } else {
                            // Image flow (existing behavior)
                            const sliderImage = formData.get("slider_image[]");
                            const hasUploadedImage = sliderImage instanceof File && sliderImage.name !== '';

                            if (! this.hasSliderImage(formData, hasUploadedImage)) {
                                throw new Error("{{ trans('admin::app.settings.themes.edit.slider-required') }}");
                            }

                            const sliderIndex = this.upsertSlider(sliderData);

                            if (hasUploadedImage) {
                                this.setFile(sliderImage, sliderIndex);
                                this.markSliderImageForDeletion();
                            }
                        }

                        resetForm();
                        this.resetSelectedSlider();
                        this.$refs.addSliderModal.toggle();

                    } catch (error) {
                        setErrors({
                            slider_image: mediaType !== 'video' ? [error.message] : [],
                            slider_video: mediaType === 'video' ? [error.message] : [],
                        });
                    }
                },

                upsertSlider(sliderData) {
                    if (this.isUpdating) {
                        this.sliders.images[this.selectedSliderIndex] = {
                            ...this.sliders.images[this.selectedSliderIndex],
                            ...sliderData,
                        };

                        return this.selectedSliderIndex;
                    }

                    this.sliders.images.push(sliderData);

                    return this.sliders.images.length - 1;
                },

                markSliderImageForDeletion() {
                    if (! this.isUpdating || ! this.selectedSliderOriginalImage) {
                        return;
                    }

                    this.deletedSliders.push({
                        image: this.selectedSliderOriginalImage,
                    });
                },

                hasSliderImage(formData, hasUploadedImage) {
                    if (hasUploadedImage) {
                        return true;
                    }

                    return Array.from(formData.keys()).some((key) => {
                        return key === 'slider_image[]' || key.startsWith('slider_image[');
                    });
                },

                onVideoFileSelected(event) {
                    const file = event.target.files[0];

                    if (file) {
                        this.selectedVideoFile = file;
                    }
                },

                setFile(file, index) {
                    const dataTransfer = new DataTransfer();

                    dataTransfer.items.add(file);

                    setTimeout(() => {
                        if (this.$refs['image_' + index] && this.$refs['image_' + index][0]) {
                            this.$refs['image_' + index][0].href = URL.createObjectURL(file);
                        }

                        if (this.$refs['imageName_' + index] && this.$refs['imageName_' + index][0]) {
                            this.$refs['imageName_' + index][0].innerHTML = file.name;
                        }

                        if (this.$refs['imageInput_' + index] && this.$refs['imageInput_' + index][0]) {
                            this.$refs['imageInput_' + index][0].files = dataTransfer.files;
                        }
                    }, 0);
                },

                remove(index) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            const slider = this.sliders.images[index];

                            if (! slider) {
                                return;
                            }

                            if (slider.image) {
                                this.deletedSliders.push(slider);
                            }

                            this.sliders.images.splice(index, 1);
                        },
                    });
                },

                create() {
                    this.openSliderModal();
                },

                edit(slider, index) {
                    this.openSliderModal(slider, index);
                },

                openSliderModal(slider = null, index = null) {
                    this.resetSelectedSlider();

                    if (slider) {
                        this.isUpdating = true;
                        this.selectedSliderIndex = index;
                        this.selectedSlider = { ...slider, type: slider.type || 'image' };
                        this.selectedSliderOriginalImage = slider.image;

                        if ((slider.type || 'image') === 'image' && slider.image) {
                            this.selectedSliderMediaImages = [{ id: `slider_image_${index}`, url: '{{ asset('/') }}' + slider.image }];
                        } else {
                            this.selectedSliderMediaImages = [];
                        }
                    }

                    this.mediaComponentKey++;

                    this.$refs.addSliderModal.toggle();
                },

                resetSelectedSlider() {
                    this.selectedSlider = { type: 'image' };
                    this.selectedSliderMediaImages = [];
                    this.selectedSliderOriginalImage = null;
                    this.selectedVideoFile = null;
                    this.selectedSliderIndex = null;
                    this.isUpdating = false;
                },
            },
        });
    </script>
@endPushOnce    
