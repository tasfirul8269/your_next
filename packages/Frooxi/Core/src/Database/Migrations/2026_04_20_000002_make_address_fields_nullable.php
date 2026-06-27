<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Make street address nullable since we simplified the form
            $table->string('address')->nullable()->change();

            // Ensure country, state, postcode, company_name, vat_id are nullable (they already are, but being explicit)
            $table->string('company_name')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('postcode')->nullable()->change();
            $table->string('vat_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
        });
    }
};
