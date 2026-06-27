<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Makes Bangladeshi Taka (৳) the store's base currency.
 *
 * A fresh install seeds whatever `APP_CURRENCY` is set to (USD by default), so
 * this seeder converts the channel's base currency to BDT in place. Idempotent
 * and safe to re-run; it does not touch product prices (those are stored as
 * plain numbers and simply render with the new symbol).
 */
class BdtCurrencySeeder extends Seeder
{
    public function run(): void
    {
        $existing = DB::table('currencies')->where('code', 'BDT')->first();

        if ($existing) {
            DB::table('currencies')->where('id', $existing->id)->update([
                'name'   => 'Bangladeshi Taka',
                'symbol' => '৳',
            ]);

            $bdtId = $existing->id;
        } else {
            // Convert the channel's current base currency row to BDT in place,
            // so existing channel/pivot references keep working.
            $channel = DB::table('channels')->first();

            $baseId = $channel->base_currency_id
                ?? DB::table('currencies')->min('id');

            if (! $baseId) {
                $baseId = DB::table('currencies')->insertGetId([
                    'code'   => 'BDT',
                    'name'   => 'Bangladeshi Taka',
                    'symbol' => '৳',
                ]);
            } else {
                DB::table('currencies')->where('id', $baseId)->update([
                    'code'   => 'BDT',
                    'name'   => 'Bangladeshi Taka',
                    'symbol' => '৳',
                ]);
            }

            $bdtId = $baseId;
        }

        // Point every channel's base currency at BDT and make sure it's an allowed currency.
        DB::table('channels')->update(['base_currency_id' => $bdtId]);

        $channelIds = DB::table('channels')->pluck('id');

        foreach ($channelIds as $channelId) {
            $linked = DB::table('channel_currencies')
                ->where('channel_id', $channelId)
                ->where('currency_id', $bdtId)
                ->exists();

            if (! $linked) {
                DB::table('channel_currencies')->insert([
                    'channel_id'  => $channelId,
                    'currency_id' => $bdtId,
                ]);
            }
        }

        $this->command?->info('Base currency set to BDT (৳).');
    }
}
