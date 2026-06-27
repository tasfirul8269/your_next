<div class="flex flex-col">
    <p 
        class="font-semibold leading-6 text-gray-800 dark:text-white"
        v-text="'{{ $address->name }}'"
    >
    </p>

    <p 
        class="!leading-6 text-gray-600 dark:text-gray-300"
        v-pre
    >
        {{ $address->address }}<br>

        {{ $address->city }}<br>

        {{ trans('admin::app.sales.orders.view.contact') }} : {{ $address->phone }}
    </p>
</div>