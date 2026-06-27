<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.catalog.categories.edit.title')
    </x-slot>

    @php
        $currentLocale = core()->getRequestedLocale();
    @endphp

    {!! view_render_event('frooxi.admin.catalog.categories.edit.before', ['category' => $category]) !!}

    <!-- Category Edit Form -->
    <x-admin::form
        :action="route('admin.catalog.categories.update', $category->id)"
        enctype="multipart/form-data"
        method="PUT"
    >

        {!! view_render_event('frooxi.admin.catalog.categories.edit.edit_form_controls.before', ['category' => $category]) !!}

        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <h1 class="font-serif text-2xl font-bold text-gray-900">
                @lang('admin::app.catalog.categories.edit.title')
            </h1>

            <div class="flex items-center gap-x-2.5">
                <!-- Back Button -->
                <a
                    href="{{ route('admin.catalog.categories.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('admin::app.catalog.categories.edit.back-btn')
                </a>

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('admin::app.catalog.categories.edit.save-btn')
                </button>
            </div>
        </div>

        <!-- Hidden required defaults -->
        <input type="hidden" name="locale" value="{{ $currentLocale->code }}">
        <input type="hidden" name="position" value="{{ $category->position ?: 1 }}">
        <input type="hidden" name="display_mode" value="{{ in_array($category->display_mode, ['products_and_description', 'description_only']) ? 'products_only' : ($category->display_mode ?: 'products_only') }}">
        <input type="hidden" name="status" value="1">
        @php $selectedAttributes = old('attributes') ?: $category->filterableAttributes->pluck('id')->toArray() @endphp
        @foreach ($attributes as $attribute)
            <input type="hidden" name="attributes[]" value="{{ $attribute->id }}">
        @endforeach

        <!-- Simplified Panel -->
        <div class="mt-6 flex flex-col gap-4 max-w-2xl">

            {!! view_render_event('frooxi.admin.catalog.categories.edit.card.general.before', ['category' => $category]) !!}

            <!-- General -->
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <p class="mb-4 text-base font-semibold text-gray-900 pb-3 border-b border-gray-50">
                    @lang('admin::app.catalog.categories.edit.general')
                </p>

                <!-- Name -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        @lang('admin::app.catalog.categories.edit.name')
                    </x-admin::form.control-group.label>

                    <v-field
                        type="text"
                        name="{{ $currentLocale->code }}[name]"
                        value="{{ old($currentLocale->code)['name'] ?? ($category->translate($currentLocale->code)['name'] ?? '') }}"
                        label="{{ trans('admin::app.catalog.categories.edit.name') }}"
                        rules="required"
                        v-slot="{ field }"
                    >
                        <input
                            type="text"
                            name="{{ $currentLocale->code }}[name]"
                            id="{{ $currentLocale->code }}[name]"
                            v-bind="field"
                            :class="[errors['{{ $currentLocale->code }}[name]'] ? 'border border-red-600 hover:border-red-600' : '']"
                            class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            placeholder="{{ trans('admin::app.catalog.categories.edit.name') }}"
                            v-slugify-target:{{$currentLocale->code.'[slug]'}}="setValues"
                        />
                    </v-field>

                    <x-admin::form.control-group.error control-name="{{ $currentLocale->code}}[name]" />
                </x-admin::form.control-group>

                <!-- Slug (hidden, auto-filled) -->
                <div style="display:none;">
                    <v-field
                        type="text"
                        name="{{$currentLocale->code}}[slug]"
                        rules="required"
                        value="{{ old($currentLocale->code)['slug'] ?? ($category->translate($currentLocale->code)['slug'] ?? '') }}"
                        label="{{ trans('admin::app.catalog.categories.edit.slug') }}"
                        v-slot="{ field }"
                    >
                        <input
                            type="text"
                            id="{{$currentLocale->code}}[slug]"
                            name="{{$currentLocale->code}}[slug]"
                            v-bind="field"
                            v-slugify-target:{{$currentLocale->code.'[slug]'}}
                        />
                    </v-field>
                </div>

                <!-- Parent category -->
                @if ($categories->count())
                    <div class="mt-4">
                        <label class="mb-2.5 block text-xs font-medium leading-6 text-gray-700">
                            @lang('admin::app.catalog.categories.edit.select-parent-category')
                        </label>

                        <div class="flex flex-col gap-3 text-sm text-gray-700">
                            <x-admin::tree.view
                                input-type="radio"
                                name-field="parent_id"
                                value-field="id"
                                id-field="id"
                                :items="json_encode($categories)"
                                :value="json_encode($category->parent_id)"
                                :fallback-locale="config('app.fallback_locale')"
                            />
                        </div>
                    </div>
                @endif
            </div>

            {!! view_render_event('frooxi.admin.catalog.categories.edit.card.general.after', ['category' => $category]) !!}

            <!-- Category Image (Logo) -->
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <p class="mb-4 text-base font-semibold text-gray-900 pb-3 border-b border-gray-50">
                    Category Image
                </p>

                <p class="mb-3 text-xs text-gray-500">
                    @lang('admin::app.catalog.categories.edit.logo-size')
                </p>

                <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-6 text-center hover:border-[#D4A84B] transition-colors">
                    <x-admin::media.images
                        name="logo_path"
                        :uploaded-images="$category->logo_path ? [['id' => 'logo_path', 'url' => $category->logo_url]] : []"
                    />
                </div>
            </div>

        </div>

        {!! view_render_event('frooxi.admin.catalog.categories.edit.edit_form_controls.after', ['category' => $category]) !!}

    </x-admin::form>

    {!! view_render_event('frooxi.admin.catalog.categories.edit.after', ['category' => $category]) !!}

</x-admin::layouts>
