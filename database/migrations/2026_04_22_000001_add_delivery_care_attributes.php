<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        // 1. Insert attributes
        DB::table('attributes')->updateOrInsert(
            ['code' => 'delivery_timeline'],
            [
                'admin_name'          => 'Delivery Timeline',
                'type'                => 'textarea',
                'validation'          => null,
                'position'            => 31,
                'is_required'         => 0,
                'is_unique'           => 0,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 0,
                'is_comparable'       => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]
        );

        DB::table('attributes')->updateOrInsert(
            ['code' => 'care_instructions'],
            [
                'admin_name'          => 'Care Instructions',
                'type'                => 'textarea',
                'validation'          => null,
                'position'            => 32,
                'is_required'         => 0,
                'is_unique'           => 0,
                'is_filterable'       => 0,
                'is_configurable'     => 0,
                'is_user_defined'     => 1,
                'is_visible_on_front' => 0,
                'is_comparable'       => 0,
                'value_per_locale'    => 1,
                'value_per_channel'   => 0,
                'enable_wysiwyg'      => 0,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]
        );

        $deliveryAttrId = DB::table('attributes')->where('code', 'delivery_timeline')->value('id');
        $careAttrId     = DB::table('attributes')->where('code', 'care_instructions')->value('id');

        // 2. Add attribute translations for all locales
        $locales = [
            'ar', 'bn', 'ca', 'de', 'en', 'es', 'fa', 'fr', 'he',
            'hi_IN', 'id', 'it', 'ja', 'nl', 'pl', 'pt_BR', 'ru',
            'sin', 'tr', 'uk', 'zh_CN',
        ];

        $translationRows = [];
        foreach ($locales as $locale) {
            $translationRows[] = [
                'attribute_id' => $deliveryAttrId,
                'locale'       => $locale,
                'name'         => 'Delivery Timeline',
            ];
            $translationRows[] = [
                'attribute_id' => $careAttrId,
                'locale'       => $locale,
                'name'         => 'Care Instructions',
            ];
        }

        foreach ($translationRows as $row) {
            DB::table('attribute_translations')->updateOrInsert(
                ['attribute_id' => $row['attribute_id'], 'locale' => $row['locale']],
                ['name' => $row['name']]
            );
        }

        // 3. Add to "Description" attribute group — look it up dynamically, skip if not seeded yet
        $descriptionGroupId = DB::table('attribute_groups')->where('name', 'Description')->value('id')
            ?? DB::table('attribute_groups')->value('id'); // fallback: first available group

        if (! $descriptionGroupId) {
            // Attribute groups not seeded yet — skip mapping, will be handled by DB import
        } else {
            $maxPos = DB::table('attribute_group_mappings')
                ->where('attribute_group_id', $descriptionGroupId)
                ->max('position') ?? 0;

            foreach ([$deliveryAttrId, $careAttrId] as $i => $attrId) {
                $exists = DB::table('attribute_group_mappings')
                    ->where('attribute_id', $attrId)
                    ->where('attribute_group_id', $descriptionGroupId)
                    ->exists();

                if (! $exists) {
                    DB::table('attribute_group_mappings')->insert([
                        'attribute_id'       => $attrId,
                        'attribute_group_id' => $descriptionGroupId,
                        'position'           => $maxPos + $i + 1,
                    ]);
                }
            }
        }

        // 4. Add columns to product_flat table so the EAV flat indexer works
        $schema = \Illuminate\Support\Facades\Schema::connection(config('database.default'));

        if (! $schema->hasColumn('product_flat', 'delivery_timeline')) {
            $schema->table('product_flat', function ($table) {
                $table->text('delivery_timeline')->nullable()->after('description');
            });
        }

        if (! $schema->hasColumn('product_flat', 'care_instructions')) {
            $schema->table('product_flat', function ($table) {
                $table->text('care_instructions')->nullable()->after('delivery_timeline');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = \Illuminate\Support\Facades\Schema::connection(config('database.default'));

        foreach (['delivery_timeline', 'care_instructions'] as $code) {
            $attr = DB::table('attributes')->where('code', $code)->first();
            if (! $attr) {
                continue;
            }

            DB::table('attribute_group_mappings')->where('attribute_id', $attr->id)->delete();
            DB::table('attribute_translations')->where('attribute_id', $attr->id)->delete();
            DB::table('product_attribute_values')->where('attribute_id', $attr->id)->delete();
            DB::table('attributes')->where('id', $attr->id)->delete();
        }

        foreach (['delivery_timeline', 'care_instructions'] as $col) {
            if ($schema->hasColumn('product_flat', $col)) {
                $schema->table('product_flat', fn ($t) => $t->dropColumn($col));
            }
        }
    }
};
