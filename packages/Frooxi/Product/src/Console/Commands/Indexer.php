<?php

namespace Frooxi\Product\Console\Commands;

use Frooxi\Product\Helpers\Indexers\ElasticSearch;
use Frooxi\Product\Helpers\Indexers\Flat;
use Frooxi\Product\Helpers\Indexers\Inventory;
use Frooxi\Product\Helpers\Indexers\Price;
use Illuminate\Console\Command;

class Indexer extends Command
{
    protected $indexers = [
        'inventory' => Inventory::class,
        'price' => Price::class,
        'flat' => Flat::class,
        'elastic' => ElasticSearch::class,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indexer:index {--type=*} {--mode=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically updates product price and inventory indices';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $start = microtime(true);

        $indexerIds = ['inventory', 'price', 'flat', 'elastic'];

        if (! empty($this->option('type'))) {
            $indexerIds = $this->option('type');
        }

        $mode = 'selective';

        if (! empty($this->option('mode'))) {
            $mode = current($this->option('mode'));
        }

        foreach ($indexerIds as $indexerId) {
            if (
                $indexerId == 'elastic'
                && core()->getConfigData('catalog.products.search.engine') != 'elastic'
            ) {
                continue;
            }

            $indexer = app($this->indexers[$indexerId]);

            if ($mode == 'full') {
                $indexer->reindexFull();
            } else {
                if ($indexerId != 'inventory') {
                    $indexer->reindexSelective();
                }
            }
        }

        $end = microtime(true);

        $this->components->success('The code took '.(round($end - $start, 2)).' seconds to complete.');
    }
}
