@if (Frooxi\Product\Helpers\ProductType::hasVariants($product->type))
    {!! view_render_event('frooxi.shop.products.view.configurable-options.before', ['product' => $product]) !!}

    <v-product-configurable-options :errors="errors"></v-product-configurable-options>

    {!! view_render_event('frooxi.shop.products.view.configurable-options.after', ['product' => $product]) !!}

    @push('scripts')
        <script
            type="text/x-template"
            id="v-product-configurable-options-template"
        >
            <div class="w-[455px] max-w-full max-sm:w-full">
                <input
                    type="hidden"
                    name="selected_configurable_option"
                    id="selected_configurable_option"
                    :value="selectedOptionVariant"
                    ref="selected_configurable_option"
                >

                <div
                    class="mt-5"
                    v-for='(attribute, index) in childAttributes'
                >
                    <h2 class="pdp-option-heading">
                        @{{ attribute.label }}
                    </h2>

                    <template v-if="shouldRenderAsDropdown(attribute)">
                        <v-field
                            as="select"
                            :name="'super_attribute[' + attribute.id + ']'"
                            class="custom-select mb-3 block w-full cursor-pointer rounded-lg border border-zinc-200 bg-white px-5 py-3 text-base text-zinc-500 focus:border-blue-500 focus:ring-blue-500"
                            :class="[errors['super_attribute[' + attribute.id + ']'] ? 'border border-red-500' : '']"
                            :id="'attribute_' + attribute.id"
                            v-model="attribute.selectedValue"
                            rules="required"
                            :label="attribute.label"
                            :aria-label="attribute.label"
                            :disabled="attribute.disabled"
                            @change="configure(attribute, $event.target.value)"
                        >
                            <option
                                v-for='(option, index) in attribute.options'
                                :value="option.id"
                            >
                                @{{ option.label }}
                            </option>
                        </v-field>
                    </template>

                    <template v-else-if="isColorAttribute(attribute)">
                        <div class="pdp-color-options">
                            <template v-for="(option, index) in attribute.options">
                                <template v-if="option.id">
                                    <label
                                        class="cursor-pointer"
                                        :title="option.label"
                                    >
                                        <v-field
                                            type="radio"
                                            :name="'super_attribute[' + attribute.id + ']'"
                                            :value="option.id"
                                            v-model="attribute.selectedValue"
                                            v-slot="{ field }"
                                            rules="required"
                                            :label="attribute.label"
                                            :aria-label="attribute.label"
                                        >
                                            <input
                                                type="radio"
                                                :name="'super_attribute[' + attribute.id + ']'"
                                                :value="option.id"
                                                v-bind="field"
                                                :id="'attribute_' + attribute.id + '_' + index"
                                                class="sr-only"
                                                :aria-label="option.label"
                                                @click="configure(attribute, $event.target.value)"
                                            />
                                        </v-field>

                                        <span
                                            class="pdp-color-option"
                                            :class="{ 'is-selected': option.id == attribute.selectedValue }"
                                            :style="getColorOptionStyle(option)"
                                        ></span>
                                    </label>
                                </template>
                            </template>

                            <span
                                class="text-sm text-gray-600 max-sm:text-xs"
                                v-if="! hasRenderableOptions(attribute)"
                            >
                                @lang('shop::app.products.view.type.configurable.select-above-options')
                            </span>
                        </div>
                    </template>

                    <template v-else-if="attribute.swatch_type == 'image'">
                        <div class="flex flex-wrap items-center gap-3">
                            <template v-for="(option, index) in attribute.options">
                                <template v-if="option.id">
                                    <label
                                        class="group relative flex h-[60px] w-[60px] cursor-pointer items-center justify-center overflow-hidden rounded-[12px] border bg-white"
                                        :class="{ 'border-zinc-900': option.id == attribute.selectedValue, 'border-zinc-300': option.id != attribute.selectedValue }"
                                        :title="option.label"
                                    >
                                        <v-field
                                            type="radio"
                                            :name="'super_attribute[' + attribute.id + ']'"
                                            v-model="attribute.selectedValue"
                                            :value="option.id"
                                            v-slot="{ field }"
                                            rules="required"
                                            :label="attribute.label"
                                            :aria-label="attribute.label"
                                        >
                                            <input
                                                type="radio"
                                                :name="'super_attribute[' + attribute.id + ']'"
                                                :value="option.id"
                                                v-bind="field"
                                                :id="'attribute_' + attribute.id + '_' + index"
                                                :aria-label="option.label"
                                                class="sr-only"
                                                @click="configure(attribute, $event.target.value)"
                                            />
                                        </v-field>

                                        <img
                                            :src="option.swatch_value"
                                            :title="option.label"
                                            style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;"
                                        />
                                    </label>
                                </template>
                            </template>

                            <span
                                class="text-sm text-gray-600 max-sm:text-xs"
                                v-if="! hasRenderableOptions(attribute)"
                            >
                                @lang('shop::app.products.view.type.configurable.select-above-options')
                            </span>
                        </div>
                    </template>

                    <template v-else>
                        <div class="pdp-size-options">
                            <template v-for="(option, index) in attribute.options">
                                <template v-if="option.id">
                                    <label
                                        :title="option.label"
                                        class="cursor-pointer"
                                    >
                                        <v-field
                                            type="radio"
                                            :name="'super_attribute[' + attribute.id + ']'"
                                            :value="option.id"
                                            v-model="attribute.selectedValue"
                                            v-slot="{ field }"
                                            rules="required"
                                            :label="attribute.label"
                                            :aria-label="attribute.label"
                                        >
                                            <input
                                                type="radio"
                                                :name="'super_attribute[' + attribute.id + ']'"
                                                :value="option.id"
                                                v-bind="field"
                                                :id="'attribute_' + attribute.id + '_' + index"
                                                class="sr-only"
                                                :aria-label="option.label"
                                                @click="configure(attribute, $event.target.value)"
                                            />
                                        </v-field>

                                        <span
                                            class="pdp-size-option"
                                            :class="{ 'is-selected': option.id == attribute.selectedValue }"
                                        >
                                            @{{ option.label }}
                                        </span>
                                    </label>
                                </template>
                            </template>

                            <span
                                class="text-sm text-gray-600 max-sm:text-xs"
                                v-if="! hasRenderableOptions(attribute)"
                            >
                                @lang('shop::app.products.view.type.configurable.select-above-options')
                            </span>
                        </div>
                    </template>

                    <v-error-message
                        :name="'super_attribute[' + attribute.id + ']'"
                        v-slot="{ message }"
                    >
                        <p class="mt-1 text-xs italic text-red-500">
                            @{{ message }}
                        </p>
                    </v-error-message>
                </div>
            </div>
        </script>

        <script type="module">
            let galleryImages = @json(product_image()->getGalleryImages($product));

            app.component('v-product-configurable-options', {
                template: '#v-product-configurable-options-template',

                props: ['errors'],

                data() {
                    return {
                        config: @json(app('Frooxi\Product\Helpers\ConfigurableOption')->getConfigurationConfig($product)),

                        childAttributes: [],

                        possibleOptionVariant: null,

                        selectedOptionVariant: '',

                        galleryImages: [],
                    }
                },

                mounted() {
                    let attributes = JSON.parse(JSON.stringify(this.config)).attributes.slice();

                    let index = attributes.length;

                    while (index--) {
                        let attribute = attributes[index];

                        attribute.options = [];

                        if (index) {
                            attribute.disabled = true;
                        } else {
                            this.fillAttributeOptions(attribute);
                        }

                        attribute = Object.assign(attribute, {
                            childAttributes: this.childAttributes.slice(),
                            prevAttribute: attributes[index - 1],
                            nextAttribute: attributes[index + 1]
                        });

                        this.childAttributes.unshift(attribute);
                    }

                    this.$nextTick(() => {
                        this.autoSelectFirstVariant();
                    });
                },

                methods: {
                    autoSelectFirstVariant() {
                        let availableVariantIds = Object.keys(this.config.index);
                        if (availableVariantIds.length === 0) return;

                        let firstVariantId = availableVariantIds[0];
                        let optionsToSelect = this.config.index[firstVariantId];

                        this.childAttributes.forEach(attribute => {
                            if (optionsToSelect[attribute.id]) {
                                this.configure(attribute, optionsToSelect[attribute.id]);
                            }
                        });
                    },

                    shouldRenderAsDropdown(attribute) {
                        return (! this.isColorAttribute(attribute))
                            && (! this.isSizeAttribute(attribute))
                            && (! attribute.swatch_type || attribute.swatch_type == '' || attribute.swatch_type == 'dropdown');
                    },

                    isSizeAttribute(attribute) {
                        const code = `${attribute.code || ''}`.toLowerCase();
                        const label = `${attribute.label || ''}`.toLowerCase();

                        return code.includes('size') || label.includes('size');
                    },

                    isColorAttribute(attribute) {
                        const code = `${attribute.code || ''}`.toLowerCase();
                        const label = `${attribute.label || ''}`.toLowerCase();

                        return attribute.swatch_type == 'color' || code.includes('color') || label.includes('color');
                    },

                    hasRenderableOptions(attribute) {
                        return attribute.options.some(option => option.id);
                    },

                    getColorOptionStyle(option) {
                        const color = this.normalizeColorValue(option);

                        return {
                            background: color || '#f3f4f6',
                            borderColor: ['#fff', '#ffffff', 'white', 'transparent'].includes((color || '').toLowerCase())
                                ? '#9ca3af'
                                : '#d1d5db',
                        };
                    },

                    normalizeColorValue(option) {
                        const rawValue = `${option.swatch_value || option.label || ''}`.trim();

                        if (! rawValue) {
                            return '';
                        }

                        if (rawValue.startsWith('#') || rawValue.startsWith('rgb') || rawValue.startsWith('hsl')) {
                            return rawValue;
                        }

                        const normalizedValue = rawValue.toLowerCase().replace(/[^a-z]/g, '');

                        const colorMap = {
                            offwhite: '#f8f5ee',
                            ivory: '#fffff0',
                            beige: '#d6c6ad',
                            cream: '#fff7d6',
                            navy: '#14284a',
                            skyblue: '#87ceeb',
                            lightblue: '#add8e6',
                            darkblue: '#1e3a8a',
                            wine: '#722f37',
                            burgundy: '#800020',
                            maroon: '#800000',
                            rosepink: '#d45197',
                            dustyrose: '#9c5e6d',
                            olivegreen: '#556b2f',
                            darkgreen: '#006400',
                            lightgreen: '#90ee90',
                        };

                        return colorMap[normalizedValue] || normalizedValue;
                    },

                    configure(attribute, optionId) {
                        this.possibleOptionVariant = this.getPossibleOptionVariant(attribute, optionId);

                        if (optionId) {
                            attribute.selectedValue = optionId;
                            
                            if (attribute.nextAttribute) {
                                attribute.nextAttribute.disabled = false;

                                this.clearAttributeSelection(attribute.nextAttribute);

                                this.fillAttributeOptions(attribute.nextAttribute);

                                this.resetChildAttributes(attribute.nextAttribute);
                            } else {
                                this.selectedOptionVariant = this.possibleOptionVariant;
                            }
                        } else {
                            this.clearAttributeSelection(attribute);

                            this.clearAttributeSelection(attribute.nextAttribute);

                            this.resetChildAttributes(attribute);
                        }

                        this.reloadPrice();
                        
                        this.reloadImages();
                    },

                    getPossibleOptionVariant(attribute, optionId) {
                        let matchedOptions = attribute.options.filter(option => option.id == optionId);

                        if (matchedOptions[0]?.allowedProducts) {
                            return matchedOptions[0].allowedProducts[0];
                        }

                        return undefined;
                    },

                    fillAttributeOptions(attribute) {
                        let options = this.config.attributes.find(tempAttribute => tempAttribute.id === attribute.id)?.options;

                        attribute.options = [{
                            'id': '',
                            'label': "@lang('shop::app.products.view.type.configurable.select-options')",
                            'products': []
                        }];

                        if (! options) {
                            return;
                        }

                        let prevAttributeSelectedOption = attribute.prevAttribute?.options.find(option => option.id == attribute.prevAttribute.selectedValue);

                        let index = 1;

                        for (let i = 0; i < options.length; i++) {
                            let allowedProducts = [];

                            if (prevAttributeSelectedOption) {
                                for (let j = 0; j < options[i].products.length; j++) {
                                    if (prevAttributeSelectedOption.allowedProducts && prevAttributeSelectedOption.allowedProducts.includes(options[i].products[j])) {
                                        allowedProducts.push(options[i].products[j]);
                                    }
                                }
                            } else {
                                allowedProducts = options[i].products.slice(0);
                            }

                            if (allowedProducts.length > 0) {
                                options[i].allowedProducts = allowedProducts;

                                attribute.options[index++] = options[i];
                            }
                        }
                    },

                    resetChildAttributes(attribute) {
                        if (! attribute.childAttributes) {
                            return;
                        }

                        attribute.childAttributes.forEach(function (set) {
                            set.selectedValue = null;

                            set.disabled = true;
                        });
                    },

                    clearAttributeSelection (attribute) {
                        if (! attribute) {
                            return;
                        }

                        attribute.selectedValue = null;

                        this.selectedOptionVariant = null;
                    },

                    reloadPrice () {
                        let selectedOptionCount = this.childAttributes.filter(attribute => attribute.selectedValue).length;

                        let finalPrice = document.querySelector('.final-price');

                        let regularPrice = document.querySelector('.regular-price');

                        let configVariant = this.config.variant_prices[this.possibleOptionVariant];

                        if (this.childAttributes.length == selectedOptionCount) {
                            document.querySelector('.price-label').style.display = 'none';

                            if (parseInt(configVariant.regular.price) > parseInt(configVariant.final.price)) {
                                regularPrice.style.display = 'block';

                                finalPrice.innerHTML = configVariant.final.formatted_price;

                                regularPrice.innerHTML = configVariant.regular.formatted_price;
                            } else {
                                finalPrice.innerHTML = configVariant.regular.formatted_price;

                                regularPrice.style.display = 'none';
                            }

                            this.$emitter.emit('configurable-variant-selected-event',this.possibleOptionVariant);
                        } else {
                            document.querySelector('.price-label').style.display = 'inline-block';

                            finalPrice.innerHTML = this.config.regular.formatted_price;

                            this.$emitter.emit('configurable-variant-selected-event', 0);
                        }
                    },

                    reloadImages () {
                        galleryImages.splice(0, galleryImages.length)

                        if (this.possibleOptionVariant) {
                            this.config.variant_images[this.possibleOptionVariant].forEach(function(image) {
                                galleryImages.push(image);
                            });

                            this.config.variant_videos[this.possibleOptionVariant].forEach(function(video) {
                                galleryImages.push(video);
                            });
                        }

                        this.galleryImages.forEach(function(image) {
                            galleryImages.push(image);
                        });

                        if (galleryImages.length) {
                            this.$parent.$parent.$refs.gallery.media.images =  [...galleryImages];
                        }

                        this.$emitter.emit('configurable-variant-update-images-event', galleryImages);
                    },
                }
            });

        </script>
    @endpush

@endif