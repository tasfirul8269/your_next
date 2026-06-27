<?php

use Frooxi\Attribute\Repositories\AttributeRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $attributeRepo = app(AttributeRepository::class);
        $sizeAttribute = $attributeRepo->findOneByField('code', 'size');

        if (! $sizeAttribute) {
            return;
        }

        // All sizes from the shop filter
        $sizes = [
            ['admin_name' => '1-2', 'sort_order' => 1],
            ['admin_name' => '2', 'sort_order' => 2],
            ['admin_name' => '2-3', 'sort_order' => 3],
            ['admin_name' => '3', 'sort_order' => 4],
            ['admin_name' => '3-4', 'sort_order' => 5],
            ['admin_name' => '4', 'sort_order' => 6],
            ['admin_name' => '4-5', 'sort_order' => 7],
            ['admin_name' => '5', 'sort_order' => 8],
            ['admin_name' => '5-6', 'sort_order' => 9],
            ['admin_name' => '6', 'sort_order' => 10],
            ['admin_name' => '6-7', 'sort_order' => 11],
            ['admin_name' => '6-8', 'sort_order' => 12],
            ['admin_name' => '7', 'sort_order' => 13],
            ['admin_name' => '7-8', 'sort_order' => 14],
            ['admin_name' => '8', 'sort_order' => 15],
            ['admin_name' => '8-9', 'sort_order' => 16],
            ['admin_name' => '9', 'sort_order' => 17],
            ['admin_name' => '9-10', 'sort_order' => 18],
            ['admin_name' => '10', 'sort_order' => 19],
            ['admin_name' => '10-11', 'sort_order' => 20],
            ['admin_name' => '11', 'sort_order' => 21],
            ['admin_name' => '11-12', 'sort_order' => 22],
            ['admin_name' => '12', 'sort_order' => 23],
            ['admin_name' => '12-13', 'sort_order' => 24],
            ['admin_name' => '13', 'sort_order' => 25],
            ['admin_name' => '13-14', 'sort_order' => 26],
            ['admin_name' => '14', 'sort_order' => 27],
            ['admin_name' => '14-15', 'sort_order' => 28],
            ['admin_name' => '16', 'sort_order' => 29],
            ['admin_name' => '20', 'sort_order' => 30],
            ['admin_name' => '22', 'sort_order' => 31],
            ['admin_name' => '24', 'sort_order' => 32],
            ['admin_name' => '26', 'sort_order' => 33],
            ['admin_name' => '28', 'sort_order' => 34],
            ['admin_name' => '30', 'sort_order' => 35],
            ['admin_name' => '32', 'sort_order' => 36],
            ['admin_name' => '34', 'sort_order' => 37],
            ['admin_name' => '36', 'sort_order' => 38],
            ['admin_name' => '38', 'sort_order' => 39],
            ['admin_name' => '40', 'sort_order' => 40],
            ['admin_name' => '42', 'sort_order' => 41],
            ['admin_name' => '44', 'sort_order' => 42],
            ['admin_name' => '46', 'sort_order' => 43],
            ['admin_name' => '48', 'sort_order' => 44],
            ['admin_name' => '50', 'sort_order' => 45],
            ['admin_name' => '52', 'sort_order' => 46],
            ['admin_name' => '54', 'sort_order' => 47],
            ['admin_name' => '56', 'sort_order' => 48],
            ['admin_name' => 'Free', 'sort_order' => 49],
            ['admin_name' => 'L', 'sort_order' => 50],
            ['admin_name' => 'M', 'sort_order' => 51],
            ['admin_name' => 'S', 'sort_order' => 52],
            ['admin_name' => 'semi-stitched', 'sort_order' => 53],
            ['admin_name' => 'Unstitched', 'sort_order' => 54],
            ['admin_name' => 'XL', 'sort_order' => 55],
            ['admin_name' => 'XXL', 'sort_order' => 56],
        ];

        foreach ($sizes as $sizeData) {
            // Check if option already exists
            $exists = DB::table('attribute_options')
                ->where('attribute_id', $sizeAttribute->id)
                ->where('admin_name', $sizeData['admin_name'])
                ->exists();

            if (! $exists) {
                $optionId = DB::table('attribute_options')->insertGetId([
                    'attribute_id' => $sizeAttribute->id,
                    'admin_name' => $sizeData['admin_name'],
                    'sort_order' => $sizeData['sort_order'],
                ]);

                // Add translations
                DB::table('attribute_option_translations')->insert([
                    'attribute_option_id' => $optionId,
                    'locale' => 'en',
                    'label' => $sizeData['admin_name'],
                ]);

                // Add Bengali translation
                DB::table('attribute_option_translations')->insert([
                    'attribute_option_id' => $optionId,
                    'locale' => 'bn',
                    'label' => $sizeData['admin_name'],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing - we don't want to remove sizes on rollback
    }
};
