<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tables and columns that store polymorphic class names (morph types).
     */
    protected array $morphColumns = [
        'order_items' => ['product_type'],
        'invoice_items' => ['product_type'],
        'shipment_items' => ['product_type'],
        'refund_items' => ['product_type'],
        'cart_items' => ['product_type'],
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
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
                    ->where($column, 'LIKE', 'Frooxi%')
                    ->update([
                        $column => DB::raw("REPLACE(`{$column}`, 'Frooxi', 'Webkul')"),
                    ]);
            }
        }
    }
};
