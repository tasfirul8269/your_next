<x-admin::layouts>
    <x-slot:title>
        @lang('shipping::app.index.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="text-xl font-bold text-gray-800 dark:text-white max-sm:text-lg">
            @lang('shipping::app.index.title')
        </p>

        <button
            type="button"
            class="primary-button max-sm:w-full"
            onclick="openModal()"
        >
            @lang('shipping::app.index.add-btn')
        </button>
    </div>

    <!-- Shipping Methods Table -->
    <div class="mt-4 rounded-lg bg-white p-3 sm:p-4 shadow dark:bg-gray-800 overflow-x-auto">
        <table class="w-full text-left min-w-[600px]">
            <thead>
                <tr class="border-b dark:border-gray-700">
                    <th class="px-4 py-3 font-semibold text-gray-800 dark:text-white">@lang('shipping::app.name')</th>
                    <th class="px-4 py-3 font-semibold text-gray-800 dark:text-white">@lang('shipping::app.description')</th>
                    <th class="px-4 py-3 font-semibold text-gray-800 dark:text-white">@lang('shipping::app.price')</th>
                    <th class="px-4 py-3 font-semibold text-gray-800 dark:text-white">@lang('shipping::app.sort-order')</th>
                    <th class="px-4 py-3 font-semibold text-gray-800 dark:text-white">@lang('shipping::app.status')</th>
                    <th class="px-4 py-3 font-semibold text-gray-800 dark:text-white">@lang('shipping::app.actions')</th>
                </tr>
            </thead>
            <tbody id="shippingMethodsTable">
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        Loading...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Modal Form --}}
    <div
        class="fixed inset-0 hidden bg-gray-500/50 p-3 sm:p-5"
        id="shippingMethodModal"
        style="z-index: 99999;"
    >
        <div class="flex items-center justify-center min-h-full">
            <div class="w-full max-w-2xl rounded-lg bg-white p-4 sm:p-6 shadow-lg dark:bg-gray-800 max-h-[90vh] overflow-y-auto">
            <form
                id="shippingMethodForm"
                action="{{ route('admin.shipping_methods.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white" id="modalTitle">
                        @lang('shipping::app.create.title')
                    </h3>
                    <button type="button" class="cursor-pointer text-2xl" onclick="closeModal()">✕</button>
                </div>

                <div class="grid gap-4">
                    <!-- Name -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('shipping::app.name')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="name"
                            value=""
                            id="nameField"
                            rules="required"
                            :label="trans('shipping::app.name')"
                            :placeholder="trans('shipping::app.name')"
                        />

                        <x-admin::form.control-group.error name="name" />
                    </x-admin::form.control-group>

                    <!-- Description -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('shipping::app.description')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="textarea"
                            name="description"
                            value=""
                            id="descriptionField"
                            :label="trans('shipping::app.description')"
                            :placeholder="trans('shipping::app.description')"
                        />
                    </x-admin::form.control-group>

                    <!-- Price -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('shipping::app.price')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="price"
                            value="0"
                            id="priceField"
                            rules="required|numeric|min:0"
                            :label="trans('shipping::app.price')"
                            :placeholder="trans('shipping::app.price')"
                        />

                        <x-admin::form.control-group.error name="price" />
                    </x-admin::form.control-group>

                    <!-- Sort Order -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label>
                            @lang('shipping::app.sort-order')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="sort_order"
                            value="0"
                            id="sortOrderField"
                            :label="trans('shipping::app.sort-order')"
                        />
                    </x-admin::form.control-group>

                    <!-- Status -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('shipping::app.status')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="switch"
                            name="status"
                            value="1"
                            id="statusField"
                            :label="trans('shipping::app.status')"
                        />
                    </x-admin::form.control-group>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button" class="transparent-button" onclick="closeModal()">
                        @lang('shipping::app.cancel')
                    </button>
                    <button type="submit" class="primary-button" id="submitBtn">
                        @lang('shipping::app.save-btn')
                    </button>
                </div>

                <input type="hidden" name="_method" id="methodField" value="POST">
            </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Store shipping methods data
        var shippingMethodsData = [];

        // Load shipping methods on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, checking elements...');
            console.log('Modal exists:', document.getElementById('shippingMethodModal'));
            console.log('Form exists:', document.getElementById('shippingMethodForm'));
            console.log('Name field exists:', document.getElementById('nameField'));
            console.log('Modal title exists:', document.getElementById('modalTitle'));
            loadShippingMethods();
        });

        function loadShippingMethods() {
            axios.get("{{ route('admin.shipping_methods.index') }}")
                .then(response => {
                    shippingMethodsData = response.data.data || [];
                    renderTable();
                })
                .catch(error => {
                    console.error('Error loading shipping methods:', error);
                    document.getElementById('shippingMethodsTable').innerHTML = `
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-red-500">
                                Error loading data
                            </td>
                        </tr>
                    `;
                });
        }

        function renderTable() {
            const tbody = document.getElementById('shippingMethodsTable');
            
            if (shippingMethodsData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No shipping methods found. Click "Add Shipping Method" to create one.
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = shippingMethodsData.map(method => `
                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3">${method.name}</td>
                    <td class="px-4 py-3">${method.description || '-'}</td>
                    <td class="px-4 py-3">${method.price}</td>
                    <td class="px-4 py-3">${method.sort_order}</td>
                    <td class="px-4 py-3">${method.status}</td>
                    <td class="px-4 py-3">${method.actions}</td>
                </tr>
            `).join('');
        }

        function openModal() {
            console.log('openModal called');
            console.log('Modal:', document.getElementById('shippingMethodModal'));
            console.log('Form:', document.getElementById('shippingMethodForm'));
            console.log('Name field:', document.getElementById('nameField'));
            console.log('Modal title:', document.getElementById('modalTitle'));
            
            var modalTitle = document.getElementById('modalTitle');
            if (modalTitle) {
                modalTitle.textContent = "{{ trans('shipping::app.create.title') }}";
            }
            
            var methodField = document.getElementById('methodField');
            if (methodField) {
                methodField.value = 'POST';
            }
            
            var form = document.getElementById('shippingMethodForm');
            if (form) {
                form.action = "{{ route('admin.shipping_methods.store') }}";
            }
            
            var nameField = document.getElementById('nameField');
            if (nameField) {
                nameField.value = '';
            }
            
            var descField = document.getElementById('descriptionField');
            if (descField) {
                descField.value = '';
            }
            
            var priceField = document.getElementById('priceField');
            if (priceField) {
                priceField.value = '0';
            }
            
            var sortOrderField = document.getElementById('sortOrderField');
            if (sortOrderField) {
                sortOrderField.value = '0';
            }
            
            var statusField = document.getElementById('statusField');
            if (statusField) {
                statusField.value = '1';
            }
            
            var modal = document.getElementById('shippingMethodModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeModal() {
            var modal = document.getElementById('shippingMethodModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function editShippingMethod(id) {
            const method = shippingMethodsData.find(m => m.id == id);
            
            if (method) {
                var modalTitle = document.getElementById('modalTitle');
                if (modalTitle) {
                    modalTitle.textContent = "{{ trans('shipping::app.edit.title') }}";
                }
                
                var methodField = document.getElementById('methodField');
                if (methodField) {
                    methodField.value = 'PUT';
                }
                
                var form = document.getElementById('shippingMethodForm');
                if (form) {
                    form.action = "{{ route('admin.shipping_methods.update', ':id') }}".replace(':id', id);
                }
                
                var nameField = document.getElementById('nameField');
                if (nameField) {
                    nameField.value = method.name;
                }
                
                var descField = document.getElementById('descriptionField');
                if (descField) {
                    descField.value = method.description || '';
                }
                
                var priceField = document.getElementById('priceField');
                if (priceField) {
                    priceField.value = method.price;
                }
                
                var sortOrderField = document.getElementById('sortOrderField');
                if (sortOrderField) {
                    sortOrderField.value = method.sort_order;
                }
                
                var statusField = document.getElementById('statusField');
                if (statusField) {
                    statusField.value = method.status.includes('Enable') ? '1' : '0';
                }
                
                var modal = document.getElementById('shippingMethodModal');
                if (modal) {
                    modal.classList.remove('hidden');
                }
            }
        }

        function deleteShippingMethod(id) {
            if (confirm("{{ trans('shipping::app.delete-confirm') }}")) {
                axios.delete("{{ route('admin.shipping_methods.delete', ':id') }}".replace(':id', id))
                    .then(response => {
                        alert(response.data.message);
                        loadShippingMethods();
                    });
            }
        }

        var form = document.getElementById('shippingMethodForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                const formData = new FormData(this);
                const method = document.getElementById('methodField').value;

                axios({
                    method: 'post',
                    url: this.action,
                    data: formData,
                    headers: { 
                        'X-HTTP-Method-Override': method,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Show toast notification
                    if (window.showToast) {
                        showToast('success', response.data.message || 'Success');
                    } else {
                        alert(response.data.message || 'Success');
                    }
                    closeModal();
                    loadShippingMethods();
                })
                .catch(error => {
                    const message = error.response?.data?.message || 'Error occurred';
                    if (window.showToast) {
                        showToast('error', message);
                    } else {
                        alert(message);
                    }
                });
            });
        }
    </script>
    @endpush
</x-admin::layouts>
