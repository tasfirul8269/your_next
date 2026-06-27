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
        // Update channel name in channel_translations table
        DB::table('channel_translations')
            ->where('name', 'Demo Store')
            ->update(['name' => 'Your Next Outfits']);

        // Also update SEO meta title if it exists
        DB::table('channel_translations')
            ->where('home_seo', 'LIKE', '%Demo store%')
            ->get()
            ->each(function ($translation) {
                $seo = json_decode($translation->home_seo, true);
                if (isset($seo['meta_title'])) {
                    $seo['meta_title'] = str_replace('Demo store', 'Your Next Outfits', $seo['meta_title']);
                    DB::table('channel_translations')
                        ->where('id', $translation->id)
                        ->update(['home_seo' => json_encode($seo)]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert channel name back to Demo Store
        DB::table('channel_translations')
            ->where('name', 'Your Next Outfits')
            ->update(['name' => 'Demo Store']);
    }
};
