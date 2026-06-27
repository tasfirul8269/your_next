<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = Carbon::now();

        $attributeId = DB::table('attributes')->insertGetId([
            'code' => 'discount_percentage',
            'admin_name' => 'Discount Percentage',
            'type' => 'text',
            'validation' => 'numeric',
            'position' => 16,
            'is_required' => 0,
            'is_unique' => 0,
            'value_per_locale' => 0,
            'value_per_channel' => 0,
            'is_filterable' => 0,
            'is_configurable' => 0,
            'is_user_defined' => 1,
            'is_visible_on_front' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $locales = DB::table('locales')->pluck('code')->toArray();
        if (empty($locales)) {
            $locales = ['en'];
        }

        foreach ($locales as $locale) {
            DB::table('attribute_translations')->insert([
                'locale' => $locale,
                'name' => 'Discount Percentage',
                'attribute_id' => $attributeId,
            ]);
        }

        // Price group ID is usually 4 for the default family (id = 1)
        $priceGroup = DB::table('attribute_groups')->where('name', 'Price')->first();

        if ($priceGroup) {
            $maxPosition = DB::table('attribute_group_mappings')
                ->where('attribute_group_id', $priceGroup->id)
                ->max('position');

            DB::table('attribute_group_mappings')->insert([
                'attribute_id' => $attributeId,
                'attribute_group_id' => $priceGroup->id,
                'position' => $maxPosition ? $maxPosition + 1 : 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $attribute = DB::table('attributes')->where('code', 'discount_percentage')->first();
        if ($attribute) {
            DB::table('attribute_group_mappings')->where('attribute_id', $attribute->id)->delete();
            DB::table('attribute_translations')->where('attribute_id', $attribute->id)->delete();
            DB::table('attributes')->where('id', $attribute->id)->delete();
        }
    }
};
