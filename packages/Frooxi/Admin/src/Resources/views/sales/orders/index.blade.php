<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.sales.orders.index.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <h1 class="font-serif text-2xl font-bold text-gray-900 py-3">
            @lang('admin::app.sales.orders.index.title')
        </h1>

        <div class="flex items-center gap-x-2.5">
            <x-admin::datagrid.export src="{{ route('admin.sales.orders.index') }}" />

            {{-- Bulk Delete Button --}}
            <button
                type="button"
                id="bulk-delete-orders-btn"
                class="primary-button"
                style="display:none; background:#ef4444;"
                onclick="bulkDeleteOrders()"
            >
                Delete Selected
            </button>

            {!! view_render_event('frooxi.admin.sales.orders.create.before') !!}

            {{-- REMOVED: Admin cart creation feature not available --}}
            {{--
            @if (bouncer()->hasPermission('sales.orders.create'))
                <button
                    class="primary-button"
                    @click="$refs.selectCustomerComponent.openDrawer()"
                >
                    @lang('admin::app.sales.orders.index.create-btn')
                </button>
            @endif
            --}}

            {!! view_render_event('frooxi.admin.sales.orders.create.after') !!}
        </div>
    </div>

    {{-- REMOVED: Admin cart creation feature not available --}}
    {{-- <v-customer-search ref="selectCustomerComponent"></v-customer-search> --}}

    <x-admin::datagrid :src="route('admin.sales.orders.index')" :isMultiRow="true">
        <template #header="{
            isLoading,
            available,
            applied,
            selectAll,
            sort,
            performAction
        }">
            <template v-if="isLoading">
                <x-admin::shimmer.datagrid.table.head :isMultiRow="true" />
            </template>
    
            <template v-else>
                <!-- Grid Header -->
                <div class="row grid items-center border-b px-4 py-2.5 dark:border-gray-800"
                    style="grid-template-columns: 32px 130px 160px 1fr 170px 170px 90px 36px; column-gap: 12px;">
    
                    <!-- Checkbox -->
                    <input type="checkbox" id="select-all-orders" onchange="toggleAllOrders(this)" class="cursor-pointer" />

                    <!-- Order / Date -->
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                        @lang('admin::app.sales.orders.index.datagrid.order-id')
                    </p>
    
                    <!-- Status -->
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                        @lang('admin::app.sales.orders.index.datagrid.status')
                    </p>
    
                    <!-- Customer -->
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                        @lang('admin::app.sales.orders.index.datagrid.customer')
                    </p>
    
                    <!-- Total -->
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                        @lang('admin::app.sales.orders.index.datagrid.grand-total')
                    </p>
    
                    <!-- Payment -->
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                        @lang('admin::app.sales.orders.index.datagrid.pay-via')
                    </p>
    
                    <!-- Items -->
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">
                        @lang('admin::app.sales.orders.index.datagrid.items')
                    </p>
    
                    <!-- Action -->
                    <span></span>
                </div>
            </template>
        </template>
    
        <template #body="{
            isLoading,
            available,
            applied,
            selectAll,
            sort,
            performAction
        }">
            <template v-if="isLoading">
                <x-admin::shimmer.datagrid.table.body :isMultiRow="true" />
            </template>
    
            <template v-else>
                <!-- Order Rows -->
                <div
                    class="row grid items-center border-b px-4 py-3.5 transition-all hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950"
                    style="grid-template-columns: 32px 130px 160px 1fr 170px 170px 90px 36px; column-gap: 12px;"
                    v-for="record in available.records"
                >
                    <!-- Checkbox -->
                    <input type="checkbox" class="order-row-checkbox cursor-pointer" :value="record.id" onchange="updateBulkDeleteBtn()" />

                    <!-- Order ID + Date -->
                    <div class="flex flex-col gap-0.5">
                        <p class="text-sm font-bold text-gray-800 dark:text-white">
                            @{{ "@lang('admin::app.sales.orders.index.datagrid.id')".replace(':id', record.increment_id) }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            @{{ record.created_at }}
                        </p>
                    </div>
    
                    <!-- Status: read-only badge for terminal statuses, dropdown for editable ones -->
                    <div style="width: fit-content;">
                        <template v-if="['canceled', 'pending_payment', 'completed', 'closed'].includes(record.status)">
                            <span
                                class="order-status-static"
                                :class="'status-' + record.status"
                            >
                                <template v-if="record.status === 'canceled'">Canceled</template>
                                <template v-else-if="record.status === 'pending_payment'">Pending Payment</template>
                                <template v-else-if="record.status === 'completed'">Completed</template>
                                <template v-else-if="record.status === 'closed'">Closed</template>
                            </span>
                        </template>
                        <template v-else>
                            <select
                                class="order-status-badge"
                                :class="'status-' + record.status"
                                :value="record.status"
                                :data-order-id="record.id"
                                onchange="window.updateOrderStatus(this)"
                            >
                                <option value="pending">@lang('admin::app.sales.orders.index.datagrid.pending')</option>
                                <option value="processing">@lang('admin::app.sales.orders.index.datagrid.processing')</option>
                                <option value="shipped">@lang('admin::app.sales.orders.index.datagrid.shipped')</option>
                            </select>
                        </template>
                    </div>
    
                    <!-- Customer + Email -->
                    <div class="flex flex-col gap-0.5 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">
                            @{{ record.full_name }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">
                            @{{ record.customer_email }}
                        </p>
                    </div>
    
                    <!-- Grand Total -->
                    <div>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">
                            @{{ $admin.formatPrice(record.base_grand_total) }}
                        </p>
                    </div>
    
                    <!-- Payment Method -->
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @{{ record.method }}
                        </p>
                    </div>
    
                    <!-- Items -->
                    <div
                        class="text-xs"
                        v-html="record.items"
                    >
                    </div>
    
                    <!-- Actions -->
                    <div class="flex justify-end items-center gap-1">
                        <a :href="'{{ route('admin.sales.orders.view', ':id') }}'.replace(':id', record.id)" title="View">
                            <span class="icon-sort-right rtl:icon-sort-left cursor-pointer p-1.5 text-2xl hover:rounded-md hover:bg-gray-200 dark:hover:bg-gray-800"></span>
                        </a>
                        <span class="icon-delete cursor-pointer p-1.5 text-2xl hover:rounded-md hover:bg-red-100 dark:hover:bg-red-900" title="Delete" :data-order-id="record.id" onclick="window.deleteOrder(this.dataset.orderId)"></span>
                    </div>
                </div>
            </template>
        </template>
    </x-admin::datagrid>

    @include('admin::customers.customers.index.create')

    @pushOnce('scripts')
        <style>
            .order-status-badge {
                display: inline-flex;
                align-items: center;
                padding: 4px 10px;
                border-radius: 9999px;
                font-size: 11px;
                font-weight: 600;
                letter-spacing: 0.03em;
                cursor: pointer;
                border: none;
                outline: none;
                appearance: none;
                -webkit-appearance: none;
                background-image: url('data:image/svg+xml,%3Csvg xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 width%3D%2210%22 height%3D%226%22 fill%3D%22none%22%3E%3Cpath d%3D%22M1 1l4 4 4-4%22 stroke%3D%22%23666%22 stroke-width%3D%221.5%22 stroke-linecap%3D%22round%22 stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E');
                background-repeat: no-repeat;
                background-position: right 7px center;
                padding-right: 22px;
                transition: opacity .15s;
            }
            .order-status-badge:hover { opacity: .85; }
            .order-status-static {
                display: inline-flex;
                align-items: center;
                padding: 4px 10px;
                border-radius: 9999px;
                font-size: 11px;
                font-weight: 600;
                letter-spacing: 0.03em;
                cursor: default;
                user-select: none;
            }
            .status-pending         { background: #fef3c7; color: #92400e; }
            .status-processing      { background: #dbeafe; color: #1e40af; }
            .status-shipped         { background: #d1fae5; color: #065f46; }
            .status-canceled        { background: #fee2e2; color: #991b1b; }
            .status-pending_payment { background: #f3f4f6; color: #6b7280; }
        </style>
        <script
            type="text/x-template"
            id="v-customer-search-template"
        >
            <div class="">
                <!-- Search Drawer -->
                <x-admin::drawer
                    ref="searchCustomerDrawer"
                    @close="searchTerm = ''; searchedCustomers = [];"
                >
                    <!-- Drawer Header -->
                    <x-slot:header>
                        <div class="grid gap-3">
                            <p class="py-2 text-xl font-medium dark:text-white">
                                @lang('admin::app.sales.orders.index.search-customer.title')
                            </p>

                            <div class="relative w-full">
                                <input
                                    type="text"
                                    class="block w-full rounded-lg border bg-white py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 ltr:pl-3 ltr:pr-10 rtl:pl-10 rtl:pr-3"
                                    placeholder="@lang('admin::app.sales.orders.index.search-customer.search-by')"
                                    v-model.lazy="searchTerm"
                                    v-debounce="500"
                                />

                                <template v-if="isSearching">
                                    <img
                                        class="absolute top-2.5 h-5 w-5 animate-spin ltr:right-3 rtl:left-3"
                                        src="{{ frooxi_asset('images/spinner.svg') }}"
                                    />
                                </template>

                                <template v-else>
                                    <span class="icon-search pointer-events-none absolute top-1.5 flex items-center text-2xl ltr:right-3 rtl:left-3"></span>
                                </template>
                            </div>
                        </div>
                    </x-slot>

                    <!-- Drawer Content -->
                    <x-slot:content class="!p-0">
                        <div
                            class="grid max-h-[400px] overflow-y-auto"
                            v-if="searchedCustomers.length"
                        >
                            <div
                                class="grid cursor-pointer place-content-start gap-1.5 border-b border-slate-300 p-4 last:border-b-0 hover:bg-gray-100 dark:border-gray-800 dark:hover:bg-gray-950"
                                v-for="customer in searchedCustomers"
                                @click="createCart(customer)"
                            >
                                <p class="text-base font-semibold text-gray-600 dark:text-gray-300">
                                    @{{ customer.first_name + ' ' + customer.last_name }}
                                </p>

                                <p class="text-gray-500">
                                    @{{ customer.email }}
                                </p>
                            </div>
                        </div>

                        <!-- For Empty Variations -->
                        <div
                            class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10"
                            v-else
                        >
                            <!-- Placeholder Image -->
                            <img
                                src="{{ frooxi_asset('images/empty-placeholders/customers.svg') }}"
                                class="h-20 w-20 dark:mix-blend-exclusion dark:invert"
                            />

                            <!-- Add Variants Information -->
                            <div class="flex flex-col items-center gap-1.5">
                                <p class="text-base font-semibold text-gray-400">
                                    @lang('admin::app.sales.orders.index.search-customer.empty-title')
                                </p>

                                <p class="text-gray-400">
                                    @lang('admin::app.sales.orders.index.search-customer.empty-info')
                                </p>

                                <button
                                    class="secondary-button"
                                    @click="$refs.searchCustomerDrawer.close(); $refs.createCustomerComponent.openModal()"
                                >
                                    @lang('admin::app.sales.orders.index.search-customer.create-btn')
                                </button>
                            </div>
                        </div>
                    </x-slot>
                </x-admin::drawer>

                <v-create-customer-form
                    ref="createCustomerComponent"
                    @customer-created="createCart"
                ></v-create-customer-form>
            </div>
        </script>

        <script type="module">
            app.component('v-customer-search', {
                template: '#v-customer-search-template',

                data() {
                    return {
                        searchTerm: '',

                        searchedCustomers: [],

                        isSearching: false,
                    }
                },

                watch: {
                    searchTerm: function(newVal, oldVal) {
                        this.search();
                    }
                },

                methods: {
                    openDrawer() {
                        this.$refs.searchCustomerDrawer.open();
                    },

                    search() {
                        if (this.searchTerm.length <= 1) {
                            this.searchedCustomers = [];

                            return;
                        }

                        this.isSearching = true;

                        let self = this;

                        this.$axios.get("{{ route('admin.customers.customers.search') }}", {
                                params: {
                                    query: this.searchTerm,
                                }
                            })
                            .then(function(response) {
                                self.isSearching = false;

                                self.searchedCustomers = response.data.data;
                            })
                            .catch(function (error) {
                            });
                    },

                    {{-- REMOVED: Admin cart creation feature not available
                    createCart(customer) {
                        this.$axios.post("{{ route('admin.sales.cart.store') }}", {customer_id: customer.id})
                            .then(function(response) {
                                window.location.href = response.data.redirect_url;
                            })
                            .catch(function (error) {
                                this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                            });
                    },
                    --}}

                    updateOrderStatus(orderId, status) {
                        console.log('Updating order:', orderId, 'to status:', status);
                        
                        this.$axios.post(`{{ route('admin.sales.orders.update_status', ':id') }}`.replace(':id', orderId), {
                            status: status
                        })
                        .then((response) => {
                            console.log('Success:', response.data);
                            this.$emitter.emit('add-flash', { 
                                type: 'success', 
                                message: response.data.message 
                            });
                            // Reload the datagrid to show updated status
                            this.$refs.datagridRef?.fetchRecords();
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            console.error('Response:', error.response?.data);
                            this.$emitter.emit('add-flash', { 
                                type: 'error', 
                                message: error.response?.data?.message || 'Failed to update status' 
                            });
                        });
                    },
                }
            });
        </script>
        
        <script>
            // Global function for order status update (accessible from datagrid template)
            window.updateOrderStatus = function(selectElement) {
                const orderId = selectElement.dataset.orderId;
                const status = selectElement.value;
                
                console.log('Updating order:', orderId, 'to status:', status);
                
                axios.post("{{ route('admin.sales.orders.update_status', ':id') }}".replace(':id', orderId), {
                    status: status
                })
                .then((response) => {
                    console.log('Success:', response.data);
                    window.$emitter?.emit('add-flash', { 
                        type: 'success', 
                        message: response.data.message 
                    });
                })
                .catch((error) => {
                    console.error('Error:', error);
                    console.error('Response:', error.response?.data);
                    window.$emitter?.emit('add-flash', { 
                        type: 'error', 
                        message: error.response?.data?.message || 'Failed to update status' 
                    });
                });
            }

            // Toggle all order checkboxes
            window.toggleAllOrders = function(masterCheckbox) {
                document.querySelectorAll('.order-row-checkbox').forEach(cb => {
                    cb.checked = masterCheckbox.checked;
                });
                updateBulkDeleteBtn();
            }

            // Show/hide bulk delete button based on selection
            window.updateBulkDeleteBtn = function() {
                const checked = document.querySelectorAll('.order-row-checkbox:checked');
                const btn = document.getElementById('bulk-delete-orders-btn');
                if (btn) btn.style.display = checked.length > 0 ? 'inline-flex' : 'none';
            }

            // Get selected order IDs
            function getSelectedOrderIds() {
                return Array.from(document.querySelectorAll('.order-row-checkbox:checked')).map(cb => cb.value);
            }

            // Delete single order
            window.deleteOrder = function(orderId) {
                if (!confirm('Are you sure you want to delete this order?')) return;
                
                axios.delete("{{ route('admin.sales.orders.delete', ':id') }}".replace(':id', orderId))
                    .then((response) => {
                        window.$emitter?.emit('add-flash', { type: 'success', message: response.data.message });
                        location.reload();
                    })
                    .catch((error) => {
                        window.$emitter?.emit('add-flash', { type: 'error', message: error.response?.data?.message || 'Delete failed' });
                    });
            }

            // Bulk delete selected orders
            window.bulkDeleteOrders = function() {
                const ids = getSelectedOrderIds();
                if (!ids.length) return;
                if (!confirm('Are you sure you want to delete ' + ids.length + ' selected order(s)?')) return;

                axios.post("{{ route('admin.sales.orders.mass_delete') }}", { indices: ids })
                    .then((response) => {
                        window.$emitter?.emit('add-flash', { type: 'success', message: response.data.message });
                        location.reload();
                    })
                    .catch((error) => {
                        window.$emitter?.emit('add-flash', { type: 'error', message: error.response?.data?.message || 'Bulk delete failed' });
                    });
            }
        </script>
    @endPushOnce
</x-admin::layouts>
