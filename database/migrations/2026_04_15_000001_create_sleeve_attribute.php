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
        // 1. Insert or update the sleeve attribute
        DB::table('attributes')->updateOrInsert(
            ['code' => 'sleeve'],
            [
                'admin_name' => 'Sleeve',
                'type' => 'select',
                'validation' => null,
                'position' => 28,
                'is_required' => 0,
                'is_unique' => 0,
                'is_filterable' => 1,
                'is_configurable' => 1,
                'is_user_defined' => 1,
                'is_visible_on_front' => 1,
                'enable_wysiwyg' => 0,
                'is_comparable' => 0,
                'value_per_locale' => 0,
                'value_per_channel' => 0,
            ]
        );

        // 2. Get the newly inserted attribute ID
        $attributeId = DB::table('attributes')->where('code', 'sleeve')->value('id');

        // 3. Insert sleeve options and their translations
        $options = [
            ['admin_name' => 'Sleeveless',   'sort_order' => 1],
            ['admin_name' => 'Short Sleeve',  'sort_order' => 2],
            ['admin_name' => '3/4 Sleeve',    'sort_order' => 3],
            ['admin_name' => 'Long Sleeve',   'sort_order' => 4],
            ['admin_name' => 'Cap Sleeve',    'sort_order' => 5],
        ];

        foreach ($options as $option) {
            $existing = DB::table('attribute_options')
                ->where('attribute_id', $attributeId)
                ->where('admin_name', $option['admin_name'])
                ->first();

            if ($existing) {
                $optionId = $existing->id;
                DB::table('attribute_options')->where('id', $optionId)->update([
                    'sort_order' => $option['sort_order'],
                ]);
            } else {
                $optionId = DB::table('attribute_options')->insertGetId([
                    'attribute_id' => $attributeId,
                    'admin_name' => $option['admin_name'],
                    'sort_order' => $option['sort_order'],
                ]);
            }

            // 4. Insert or update English translation for each option
            DB::table('attribute_option_translations')->updateOrInsert(
                ['attribute_option_id' => $optionId, 'locale' => 'en'],
                ['label' => $option['admin_name']]
            );
        }

        // 5. Add the sleeve attribute to the "General" attribute group
        // First, check if attribute group exists
        $attributeGroup = DB::table('attribute_groups')->first();
                
        if ($attributeGroup) {
            $maxPosition = DB::table('attribute_group_mappings')
                ->where('attribute_group_id', $attributeGroup->id)
                ->max('position') ?? 0;
        
            $existingMapping = DB::table('attribute_group_mappings')
                ->where('attribute_id', $attributeId)
                ->where('attribute_group_id', $attributeGroup->id)
                ->first();
        
            if (! $existingMapping) {
                DB::table('attribute_group_mappings')->insert([
                    'attribute_id' => $attributeId,
                    'attribute_group_id' => $attributeGroup->id,
                    'position' => $maxPosition + 1,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $attribute = DB::table('attributes')->where('code', 'sleeve')->first();

        if (! $attribute) {
            return;
        }

        $attributeId = $attribute->id;

        // Remove group mapping
        DB::table('attribute_group_mappings')
            ->where('attribute_id', $attributeId)
            ->delete();

        // Get all option IDs
        $optionIds = DB::table('attribute_options')
            ->where('attribute_id', $attributeId)
            ->pluck('id');

        // Remove translations
        DB::table('attribute_option_translations')
            ->whereIn('attribute_option_id', $optionIds)
            ->delete();

        // Remove options
        DB::table('attribute_options')
            ->where('attribute_id', $attributeId)
            ->delete();

        // Remove the attribute
        DB::table('attributes')
            ->where('code', 'sleeve')
            ->delete();
    }
};
