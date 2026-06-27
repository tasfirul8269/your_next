@if (! $product->parent_id)
    @if ($channels->count() == 1)
        <input type="hidden" name="channels[]" value="{{ $channels->first()->id }}">
    @else
        {!! view_render_event('frooxi.admin.catalog.product.edit.form.channels.before', ['product' => $product]) !!}

        <!-- Panel -->
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <!-- Panel Header -->
            <p class="mb-4 flex justify-between text-lg font-semibold text-gray-900 dark:text-white">
                @lang('admin::app.catalog.products.edit.channels.title')
            </p>

            {!! view_render_event('frooxi.admin.catalog.product.edit.form.channels.controls.before', ['product' => $product]) !!}

            <!-- Panel Content -->
            <div class="text-sm text-gray-600 dark:text-gray-300">
                @php $selectedChannelsId = old('channels') ?? $product->channels->pluck('id')->toArray() @endphp
                
                @foreach (core()->getAllChannels() as $channel)
                    <x-admin::form.control-group class="!mb-2 flex items-center gap-2.5 last:!mb-0">
                        <x-admin::form.control-group.control
                            type="checkbox"
                            :id="'channels_' . $channel->id" 
                            name="channels[]"
                            rules="required"
                            :value="$channel->id"
                            :for="'channels_' . $channel->id" 
                            :label="trans('admin::app.catalog.products.edit.channels.title')"
                            :checked="in_array($channel->id, $selectedChannelsId)"
                        />

                        <label
                            class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                            for="channels_{{ $channel->id }}"
                            v-pre
                        >
                            {{ $channel->name }} 
                        </label>
                    </x-admin::form.control-group>
                @endforeach

                <x-admin::form.control-group.error control-name="channels[]" />
            </div>

            {!! view_render_event('frooxi.admin.catalog.product.edit.form.channels.controls.after', ['product' => $product]) !!}
        </div>

        {!! view_render_event('frooxi.admin.catalog.product.edit.form.channels.after', ['product' => $product]) !!}
    @endif
@endif