<?php

namespace Frooxi\Core\Console\Commands;

use Frooxi\Core\Helpers\Exchange\ExchangeRate;
use Illuminate\Console\Command;

class ExchangeRateUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange-rate:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically updates currency exchange rates';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            ExchangeRate::resolve()->updateRates();

            $this->info('Exchange rates updated successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to update exchange rates: '.$e->getMessage());
        }
    }
}
