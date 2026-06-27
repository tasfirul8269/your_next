<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete all invoices data
        if (Schema::hasTable('invoice_items')) {
            DB::table('invoice_items')->delete();
        }
        if (Schema::hasTable('invoices')) {
            DB::table('invoices')->delete();
        }
        
        // Delete all shipments data
        if (Schema::hasTable('shipment_items')) {
            DB::table('shipment_items')->delete();
        }
        if (Schema::hasTable('shipments')) {
            DB::table('shipments')->delete();
        }
        
        // Delete all refunds data
        if (Schema::hasTable('refund_items')) {
            DB::table('refund_items')->delete();
        }
        if (Schema::hasTable('refunds')) {
            DB::table('refunds')->delete();
        }
        
        // Delete all transactions data
        if (Schema::hasTable('transactions')) {
            DB::table('transactions')->delete();
        }
        
        // Reset all orders to pending status
        DB::table('orders')->update(['status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed
    }
};
