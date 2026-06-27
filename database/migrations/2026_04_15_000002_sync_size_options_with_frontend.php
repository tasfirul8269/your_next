<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The new size options to add (not including existing S, M, L, XL).
     */
    protected array $newSizes = [
        '1-2', '2', '2-3', '3', '3-4', '4', '4-5', '5', '5-6', '6',
        '6-7', '6-8', '7', '7-8', '8', '8-9', '9', '9-10', '10', '10-11',
        '11', '11-12', '12', '12-13', '13', '13-14', '14', '14-15', '16',
        '20', '22', '24', '26', '28', '30', '32', '34', '36', '38', '40',
        '42', '44', '46', '48', '50', '52', '54', '56', 'Free', 'XXL',
        'semi-stitched', 'Unstitched',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $attribute = DB::table('attributes')
            ->where('code', 'size')
            ->first();

        if (! $attribute) {
            return;
        }

        $attributeId = $attribute->id;

        $existing = DB::table('attribute_options')
            ->where('attribute_id', $attributeId)
            ->pluck('admin_name')
            ->map(fn ($v) => strtolower((string) $v))
            ->all();

        $sortOrder = DB::table('attribute_options')
            ->where('attribute_id', $attributeId)
            ->max('sort_order') ?? 0;

        foreach ($this->newSizes as $size) {
            if (in_array(strtolower($size), $existing, true)) {
                continue;
            }

            $sortOrder++;

            $optionId = DB::table('attribute_options')->insertGetId([
                'attribute_id' => $attributeId,
                'admin_name' => $size,
                'sort_order' => $sortOrder,
            ]);

            DB::table('attribute_option_translations')->insert([
                'attribute_option_id' => $optionId,
                'locale' => 'en',
                'label' => $size,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally no-op: this migration only adds size options.
        // Rolling back should not delete options that may have existed
        // before this migration or been assigned to products.
    }
};
