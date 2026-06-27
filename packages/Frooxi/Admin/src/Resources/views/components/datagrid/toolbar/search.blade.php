<v-datagrid-search
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @search="search"
>
    {{ $slot }}
</v-datagrid-search>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-search-template"
    >
        <!-- Empty slot for left toolbar before -->
        <slot name="left-toolbar-left-before"></slot>
        
        <slot
            name="search"
            :available="available"
            :applied="applied"
            :search="search"
            :get-searched-values="getSearchedValues"
        >
            <template v-if="isLoading">
                <x-admin::shimmer.datagrid.toolbar.search />
            </template>

            <template v-else>
                <div class="flex w-full items-center gap-x-1">
                    <!-- Search Panel -->
                    <div class="flex max-w-[445px] items-center max-sm:w-full max-sm:max-w-full">
                        <div class="relative w-full">
                            <input
                                type="text"
                                name="search"
                                :value="getSearchedValues('all')"
                                class="block w-full rounded-lg border border-gray-200 bg-gray-50/50 py-2 px-3.5 text-sm leading-6 text-gray-600 transition-all hover:border-gray-300 focus:border-[#D4A84B] focus:ring-2 focus:ring-[#D4A84B]/20 focus:bg-white focus:outline-none ltr:pr-10 rtl:pl-10"
                                :class="getSearchedValues('all')?.length ? 'border-[#D4A84B]' : ''"
                                placeholder="@lang('admin::app.components.datagrid.toolbar.search.title')"
                                autocomplete="off"
                                @keyup.enter="search"
                            >

                            <div class="icon-search pointer-events-none absolute top-2 flex items-center text-2xl ltr:right-2.5 rtl:left-2.5">
                            </div>
                        </div>
                    </div>

                    <!-- Information Panel -->
                    <div class="ltr:pl-2.5 rtl:pr-2.5">
                        <p class="text-sm font-light text-gray-800 dark:text-white">
                            @{{ "@lang('admin::app.components.datagrid.toolbar.results')".replace(':total', available.meta.total) }}
                        </p>
                    </div>
                </div>
            </template>
        </slot>

        <!-- Empty slot for left toolbar after -->
        <slot name="left-toolbar-left-after"></slot>
    </script>

    <script type="module">
        app.component('v-datagrid-search', {
            template: '#v-datagrid-search-template',

            props: ['isLoading', 'available', 'applied'],

            emits: ['search'],

            data() {
                return {
                    filters: {
                        columns: [],
                    },
                };
            },

            mounted() {
                this.filters.columns = this.applied.filters.columns.filter((column) => column.index === 'all');
            },

            methods: {
                /**
                 * Perform a search operation based on the input value.
                 *
                 * @param {Event} $event
                 * @returns {void}
                 */
                search($event) {
                    let requestedValue = $event.target.value;

                    let appliedColumn = this.filters.columns.find(column => column.index === 'all');

                    if (! requestedValue) {
                        appliedColumn.value = [];

                        this.$emit('search', this.filters);

                        return;
                    }

                    if (appliedColumn) {
                        appliedColumn.value = [requestedValue];
                    } else {
                        this.filters.columns.push({
                            index: 'all',
                            value: [requestedValue]
                        });
                    }

                    this.$emit('search', this.filters);
                },

                /**
                 * Get the searched values for a specific column.
                 *
                 * @param {string} columnIndex
                 * @returns {Array}
                 */
                getSearchedValues(columnIndex) {
                    let appliedColumn = this.filters.columns.find(column => column.index === 'all');

                    return appliedColumn?.value ?? [];
                },
            },
        });
    </script>
@endPushOnce
