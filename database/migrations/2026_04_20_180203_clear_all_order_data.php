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
        // Delete all order-related data in correct order (foreign key constraints)
        
        // Order items
        if (Schema::hasTable('order_items')) {
            DB::table('order_items')->delete();
        }
        
        // Order addresses
        if (Schema::hasTable('addresses')) {
            DB::table('addresses')->where('address_type', 'like', 'order_%')->delete();
        }
        
        // Order comments
        if (Schema::hasTable('order_comments')) {
            DB::table('order_comments')->delete();
        }
        
        // Order payment
        if (Schema::hasTable('order_payment')) {
            DB::table('order_payment')->delete();
        }
        
        // Downloadable link purchases
        if (Schema::hasTable('downloadable_link_purchases')) {
            DB::table('downloadable_link_purchases')->delete();
        }
        
        // Finally delete all orders
        if (Schema::hasTable('orders')) {
            DB::table('orders')->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse operation needed
    }
};
