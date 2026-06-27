@props([
    'isActive' => true,
])

<div {{ $attributes->merge(['class' => 'border-b border-zinc-200']) }}>
    <v-accordion
        {{ $attributes->except('class') }}
        is-active="{{ $isActive }}"
    >
        @isset($header)
            <template v-slot:header="{ toggle, isOpen }">
                <div
                    {{ $header->attributes->merge(['class' => 'flex cursor-pointer select-none items-center justify-between p-4']) }}
                    role="button"
                    tabindex="0"
                    @click="toggle"
                >
                    {{ $header }}

                    <span
                        v-bind:class="isOpen ? 'text-2xl' : 'text-2xl'"
                        v-text="isOpen ? '\u2212' : '+'"
                        role="button"
                        aria-label="Toggle accordion"
                        tabindex="0"
                    ></span>
                </div>
            </template>
        @endisset

        @isset($content)
            <template v-slot:content="{ isOpen }">
                <div
                    {{ $content->attributes->merge(['class' => 'z-10 rounded-lg bg-white p-1.5']) }}
                >
                    {{ $content }}
                </div>
            </template>
        @endisset
    </v-accordion>
</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-accordion-template"
    >
        <div>
            <slot
                name="header"
                :toggle="toggle"
                :isOpen="isOpen"
            >
                @lang('shop::app.components.accordion.default-content')
            </slot>

            <div
                ref="accordionContent"
                style="overflow: hidden; transition: max-height 0.3s ease;"
                :style="{ maxHeight: contentHeight }"
            >
                <div ref="accordionInner">
                    <slot
                        name="content"
                        :isOpen="isOpen"
                    >
                        @lang('shop::app.components.accordion.default-content')
                    </slot>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-accordion', {
            template: '#v-accordion-template',

            props: [
                'isActive',
            ],

            data() {
                const open = this.isActive === true || this.isActive === 'true' || this.isActive === '1' || this.isActive === 1;

                return {
                    isOpen: open,
                    contentHeight: open ? 'none' : '0px',
                };
            },

            mounted() {
                this.$nextTick(() => {
                    this.updateHeight();
                });
            },

            watch: {
                isOpen() {
                    this.updateHeight();
                },
            },

            methods: {
                toggle() {
                    this.isOpen = ! this.isOpen;

                    this.$emit('toggle', { isActive: this.isOpen });
                },

                updateHeight() {
                    const inner = this.$refs.accordionInner;

                    if (inner) {
                        if (this.isOpen) {
                            this.contentHeight = inner.scrollHeight + 'px';

                            setTimeout(() => {
                                if (this.isOpen) {
                                    this.contentHeight = 'none';
                                }
                            }, 350);
                        } else {
                            this.contentHeight = inner.scrollHeight + 'px';

                            requestAnimationFrame(() => {
                                requestAnimationFrame(() => {
                                    this.contentHeight = '0px';
                                });
                            });
                        }
                    }
                },
            },
        });
    </script>
@endPushOnce
