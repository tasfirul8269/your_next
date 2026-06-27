<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.catalog.categories.create.title')
    </x-slot>

    {!! view_render_event('frooxi.admin.catalog.categories.create.before') !!}

    <!-- Category Create Form -->
    <x-admin::form
        :action="route('admin.catalog.categories.store')"
        enctype="multipart/form-data"
    >
        {!! view_render_event('frooxi.admin.catalog.categories.create.create_form_controls.before') !!}

        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <h1 class="font-serif text-2xl font-bold text-gray-900">
                @lang('admin::app.catalog.categories.create.title')
            </h1>

            <div class="flex items-center gap-x-2.5">
                <!-- Back Button -->
                <a
                    href="{{ route('admin.catalog.categories.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('admin::app.catalog.categories.create.back-btn')
                </a>

                <!-- Save Button -->
                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('admin::app.catalog.categories.create.save-btn')
                </button>
            </div>
        </div>

        <!-- Hidden required defaults -->
        <x-admin::form.control-group.control type="hidden" name="locale" value="all" />
        <x-admin::form.control-group.control type="hidden" name="position" value="1" />
        <x-admin::form.control-group.control type="hidden" name="display_mode" value="products_only" />
        <x-admin::form.control-group.control type="hidden" name="status" value="1" />
        @foreach ($attributes as $attribute)
            <input type="hidden" name="attributes[]" value="{{ $attribute->id }}">
        @endforeach

        <!-- Simplified Panel -->
        <div class="mt-6 flex flex-col gap-4 max-w-2xl">

            {!! view_render_event('frooxi.admin.catalog.categories.create.card.general.before') !!}

            <!-- General -->
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <p class="mb-4 text-base font-semibold text-gray-900 pb-3 border-b border-gray-50">
                    @lang('admin::app.catalog.categories.create.general')
                </p>

                <!-- Name -->
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label class="required">
                        @lang('admin::app.catalog.categories.create.name')
                    </x-admin::form.control-group.label>

                    <v-field
                        type="text"
                        name="name"
                        rules="required"
                        value="{{ old('name') }}"
                        v-slot="{ field, errors }"
                        label="{{ trans('admin::app.catalog.categories.create.name') }}"
                    >
                        <input
                            type="text"
                            id="name"
                            :class="[errors.length ? 'border border-red-600 hover:border-red-600' : '']"
                            class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            name="name"
                            v-bind="field"
                            placeholder="{{ trans('admin::app.catalog.categories.create.name') }}"
                            v-slugify-target:slug="setValues"
                        />
                    </v-field>

                    <x-admin::form.control-group.error control-name="name" />
                </x-admin::form.control-group>

                <!-- Slug (hidden, auto-filled by slugify) -->
                <div style="display:none;">
                    <v-field
                        type="text"
                        name="slug"
                        rules="required"
                        value="{{ old('slug') }}"
                        label="{{ trans('admin::app.catalog.categories.create.slug') }}"
                        v-slot="{ field }"
                    >
                        <input
                            type="text"
                            id="slug"
                            name="slug"
                            v-bind="field"
                            v-slugify-target:slug
                        />
                    </v-field>
                </div>

                <!-- Parent category -->
                <div class="mt-4">
                    <label class="mb-2.5 block text-xs font-medium leading-6 text-gray-700">
                        @lang('admin::app.catalog.categories.create.parent-category')
                    </label>

                    <div class="flex flex-col gap-3 text-sm text-gray-700">
                        <x-admin::tree.view
                            input-type="radio"
                            id-field="id"
                            name-field="parent_id"
                            value-field="id"
                            :items="json_encode($categories)"
                            :fallback-locale="config('app.fallback_locale')"
                        />
                    </div>
                </div>
            </div>

            {!! view_render_event('frooxi.admin.catalog.categories.create.card.general.after') !!}

            <!-- Category Image (Logo) -->
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <p class="mb-4 text-base font-semibold text-gray-900 pb-3 border-b border-gray-50">
                    Category Image
                </p>

                <p class="mb-3 text-xs text-gray-500">
                    @lang('admin::app.catalog.categories.create.logo-size')
                </p>

                <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-6 text-center hover:border-[#D4A84B] transition-colors">
                    <x-admin::media.images name="logo_path" />
                </div>
            </div>

        </div>

        {!! view_render_event('frooxi.admin.catalog.categories.create.create_form_controls.after') !!}

    </x-admin::form>

    {!! view_render_event('frooxi.admin.catalog.categories.create.after') !!}

</x-admin::layouts>
