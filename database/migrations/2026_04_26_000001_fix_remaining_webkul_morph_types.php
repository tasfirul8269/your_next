<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables and columns that still store old Webkul morph class names.
     */
    protected array $morphColumns = [
        'orders' => ['channel_type'],
        'cart' => ['channel_type'],
        'invoices' => ['order_type'],
        'shipments' => ['order_type'],
        'refunds' => ['order_type'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->morphColumns as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)
                    ->where($column, 'LIKE', 'Webkul%')
                    ->update([
                        $column => DB::raw("REPLACE(`{$column}`, 'Webkul', 'Frooxi')"),
                    ]);
            }
        }

        // Also fix any remaining Webkul references in all string columns across key tables
        $tablesToScan = ['orders', 'cart', 'invoices', 'shipments', 'refunds', 'order_items', 'cart_items'];
        foreach ($tablesToScan as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $columns = Schema::getColumnListing($table);
            foreach ($columns as $column) {
                // Only process string-type columns that could contain class names
                if (str_ends_with($column, '_type')) {
                    DB::table($table)
                        ->where($column, 'LIKE', 'Webkul%')
                        ->update([
                            $column => DB::raw("REPLACE(`{$column}`, 'Webkul', 'Frooxi')"),
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed — we want Frooxi everywhere
    }
};
