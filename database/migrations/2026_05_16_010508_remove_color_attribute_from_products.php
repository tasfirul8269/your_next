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
        // Disable the color attribute by setting is_filterable and is_configurable to 0
        DB::table('attributes')
            ->where('code', 'color')
            ->update([
                'is_filterable' => 0,
                'is_configurable' => 0,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-enable the color attribute
        DB::table('attributes')
            ->where('code', 'color')
            ->update([
                'is_filterable' => 1,
                'is_configurable' => 1,
            ]);
    }
};
