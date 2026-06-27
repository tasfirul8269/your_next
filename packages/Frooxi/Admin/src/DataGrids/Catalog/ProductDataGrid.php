<?php

namespace Frooxi\Admin\DataGrids\Catalog;

use Frooxi\Admin\Exports\ProductDataGridExport;
use Frooxi\Attribute\Repositories\AttributeFamilyRepository;
use Frooxi\Core\Facades\ElasticSearch;
use Frooxi\DataGrid\DataGrid;
use Frooxi\Product\Helpers\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductDataGrid extends DataGrid
{
    /**
     * Primary column.
     *
     * @var string
     */
    protected $primaryColumn = 'product_id';

    /**
     * Constructor for the class.
     *
     * @return void
     */
    public function __construct(protected AttributeFamilyRepository $attributeFamilyRepository) {}

    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $tablePrefix = DB::getTablePrefix();

        /**
         * Query Builder to fetch records from `product_flat` table
         */
        $queryBuilder = DB::table('product_flat')
            ->distinct()
            ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
            ->leftJoin('attribute_families as af', 'product_flat.attribute_family_id', '=', 'af.id')
            ->leftJoin('product_inventories', 'product_flat.product_id', '=', 'product_inventories.product_id')
            ->leftJoin('product_images', 'product_flat.product_id', '=', 'product_images.product_id')
            ->leftJoin('product_categories as pc', 'product_flat.product_id', '=', 'pc.product_id')
            ->leftJoin('category_translations as ct', function ($leftJoin) {
                $leftJoin->on('pc.category_id', '=', 'ct.category_id')
                    ->where('ct.locale', app()->getLocale());
            })
            ->select(
                'product_flat.locale',
                'product_flat.channel',
                'product_images.path as base_image',
                'pc.category_id',
                'ct.name as category_name',
                'product_flat.product_id',
                'product_flat.sku',
                'product_flat.name',
                'product_flat.type',
                'product_flat.status',
                'product_flat.price',
                'product_flat.url_key',
                'product_flat.visible_individually',
                'af.name as attribute_family',
            )
            ->addSelect(DB::raw('SUM(DISTINCT '.$tablePrefix.'product_inventories.qty) as quantity'))
            ->addSelect(DB::raw('COUNT(DISTINCT '.$tablePrefix.'product_images.id) as images_count'))
            // Price range for configurable products (min/max across variants)
            ->addSelect(DB::raw('
                COALESCE(
                    (SELECT MIN(pf_v.price)
                     FROM '.$tablePrefix.'product_flat pf_v
                     INNER JOIN '.$tablePrefix.'products p_v ON pf_v.product_id = p_v.id
                     WHERE p_v.parent_id = '.$tablePrefix.'product_flat.product_id
                       AND pf_v.locale = '.$tablePrefix.'product_flat.locale
                       AND pf_v.price IS NOT NULL AND pf_v.price > 0),
                    '.$tablePrefix.'product_flat.price
                ) as min_price
            '))
            ->addSelect(DB::raw('
                COALESCE(
                    (SELECT MAX(pf_v.price)
                     FROM '.$tablePrefix.'product_flat pf_v
                     INNER JOIN '.$tablePrefix.'products p_v ON pf_v.product_id = p_v.id
                     WHERE p_v.parent_id = '.$tablePrefix.'product_flat.product_id
                       AND pf_v.locale = '.$tablePrefix.'product_flat.locale
                       AND pf_v.price IS NOT NULL AND pf_v.price > 0),
                    '.$tablePrefix.'product_flat.price
                ) as max_price
            '))
            // Total quantity combining all variants
            ->addSelect(DB::raw('
                COALESCE(
                    (SELECT SUM(pi_v.qty)
                     FROM '.$tablePrefix.'product_inventories pi_v
                     INNER JOIN '.$tablePrefix.'products p_v ON pi_v.product_id = p_v.id
                     WHERE p_v.parent_id = '.$tablePrefix.'product_flat.product_id),
                    SUM(DISTINCT '.$tablePrefix.'product_inventories.qty)
                ) as total_quantity
            '))
            ->where('product_flat.locale', app()->getLocale())
            ->whereNull('product_flat.parent_id');  // Only show parent products, not variations

        if (! $this instanceof \Frooxi\Admin\DataGrids\Storefront\FlashSaleProductDataGrid) {
            // Exclude flash sale products from the main product list
            $queryBuilder->where(function ($query) {
                $query->whereNull('products.flash_sale_discount')
                    ->orWhere('products.flash_sale_discount', 0);
            });
        }

        $queryBuilder->groupBy('product_flat.product_id');

        $this->addFilter('product_id', 'product_flat.product_id');
        $this->addFilter('channel', 'product_flat.channel');
        $this->addFilter('locale', 'product_flat.locale');
        $this->addFilter('name', 'product_flat.name');
        $this->addFilter('type', 'product_flat.type');
        $this->addFilter('status', 'product_flat.status');
        $this->addFilter('attribute_family', 'af.id');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $channels = core()->getAllChannels();

        if ($channels->count() > 1) {
            $this->addColumn([
                'index' => 'channel',
                'label' => trans('admin::app.catalog.products.index.datagrid.channel'),
                'type' => 'string',
                'filterable' => true,
                'filterable_type' => 'dropdown',
                'filterable_options' => collect($channels)
                    ->map(fn ($channel) => ['label' => $channel->name, 'value' => $channel->code])
                    ->values()
                    ->toArray(),
                'sortable' => true,
                'visibility' => false,
            ]);
        }

        // 1. Image
        $this->addColumn([
            'index' => 'base_image',
            'label' => trans('admin::app.catalog.products.index.datagrid.image'),
            'type' => 'string',
            'exportable' => false,
            'closure' => function ($row) {
                if (! $row->base_image) {
                    return;
                }

                if (config('filesystems.default') === 'cloudinary') {
                    return cloudinary_url($row->base_image, ['w' => 300, 'q' => 'auto']);
                }

                return Storage::url($row->base_image);
            },
        ]);

        // 2. Name
        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.catalog.products.index.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        // 3. Price — show range for configurable, flat price for others
        $this->addColumn([
            'index' => 'price',
            'label' => trans('admin::app.catalog.products.index.datagrid.price'),
            'type' => 'decimal',
            'filterable' => true,
            'sortable' => true,
            'closure' => function ($row) {
                $currency = core()->getCurrentCurrencyCode();

                if ($row->type === 'configurable') {
                    $min = (float) ($row->min_price ?? 0);
                    $max = (float) ($row->max_price ?? 0);

                    if ($min > 0 && $max > 0 && $min !== $max) {
                        return core()->formatPrice($min, $currency).' – '.core()->formatPrice($max, $currency);
                    }

                    if ($min > 0) {
                        return core()->formatPrice($min, $currency);
                    }

                    return 'N/A';
                }

                return core()->formatPrice((float) ($row->price ?? 0), $currency);
            },
        ]);

        // 4. Quantity — combined for configurable, direct for others
        $this->addColumn([
            'index' => 'quantity',
            'label' => trans('admin::app.catalog.products.index.datagrid.qty'),
            'type' => 'integer',
            'sortable' => true,
            'closure' => function ($row) {
                if ($row->type === 'configurable') {
                    $qty = (int) ($row->total_quantity ?? 0);
                } else {
                    $qty = (int) ($row->quantity ?? 0);
                }

                if ($qty > 0) {
                    return '<span class="text-green-600 font-semibold">'.$qty.' in stock</span>';
                }

                return '<span class="text-red-600 font-semibold">Out of stock</span>';
            },
        ]);

        // 5. Status
        $this->addColumn([
            'index' => 'status',
            'label' => trans('admin::app.catalog.products.index.datagrid.status'),
            'type' => 'boolean',
            'filterable' => true,
            'filterable_options' => [
                [
                    'label' => trans('admin::app.catalog.products.index.datagrid.active'),
                    'value' => 1,
                ],
                [
                    'label' => trans('admin::app.catalog.products.index.datagrid.disable'),
                    'value' => 0,
                ],
            ],
            'sortable' => true,
        ]);

        // Hidden / secondary columns
        $this->addColumn([
            'index' => 'sku',
            'label' => trans('admin::app.catalog.products.index.datagrid.sku'),
            'type' => 'string',
            'filterable' => true,
            'sortable' => true,
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'attribute_family',
            'label' => trans('admin::app.catalog.products.index.datagrid.attribute-family'),
            'type' => 'string',
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => $this->attributeFamilyRepository->all(['name as label', 'id as value'])->toArray(),
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'product_id',
            'label' => trans('admin::app.catalog.products.index.datagrid.id'),
            'type' => 'integer',
            'filterable' => true,
            'sortable' => true,
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'category_name',
            'label' => trans('admin::app.catalog.products.index.datagrid.category'),
            'type' => 'string',
            'visibility' => false,
        ]);

        $this->addColumn([
            'index' => 'type',
            'label' => trans('admin::app.catalog.products.index.datagrid.type'),
            'type' => 'string',
            'filterable' => true,
            'filterable_type' => 'dropdown',
            'filterable_options' => collect(config('product_types'))
                ->map(fn ($type) => ['label' => trans($type['name']), 'value' => $type['key']])
                ->values()
                ->toArray(),
            'sortable' => true,
            'visibility' => false,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (bouncer()->hasPermission('catalog.products.copy')) {
            $this->addAction([
                'icon' => 'icon-copy',
                'title' => trans('admin::app.catalog.products.index.datagrid.copy'),
                'method' => 'POST',
                'url' => function ($row) {
                    return route('admin.catalog.products.copy', $row->product_id);
                },
            ]);
        }

        if (bouncer()->hasPermission('catalog.products.edit')) {
            $this->addAction([
                'icon' => 'icon-edit',
                'title' => trans('admin::app.catalog.products.index.datagrid.edit'),
                'method' => 'GET',
                'url' => function ($row) {
                    $filteredChannel = request()->input('filters.channel')[0] ?? null;

                    return route('admin.catalog.products.edit', [
                        'id' => $row->product_id,
                        'channel' => $filteredChannel,
                    ]);
                },
            ]);
        }

        if (bouncer()->hasPermission('catalog.products.delete')) {
            $this->addAction([
                'icon' => 'icon-delete',
                'title' => trans('admin::app.catalog.products.index.datagrid.delete'),
                'method' => 'DELETE',
                'url' => function ($row) {
                    return route('admin.catalog.products.delete', $row->product_id);
                },
            ]);
        }
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        if (bouncer()->hasPermission('catalog.products.delete')) {
            $this->addMassAction([
                'title' => trans('admin::app.catalog.products.index.datagrid.delete'),
                'url' => route('admin.catalog.products.mass_delete'),
                'method' => 'POST',
            ]);
        }

        if (bouncer()->hasPermission('catalog.products.edit')) {
            $this->addMassAction([
                'title' => trans('admin::app.catalog.products.index.datagrid.update-status'),
                'url' => route('admin.catalog.products.mass_update'),
                'method' => 'POST',
                'options' => [
                    [
                        'label' => trans('admin::app.catalog.products.index.datagrid.active'),
                        'value' => 1,
                    ],
                    [
                        'label' => trans('admin::app.catalog.products.index.datagrid.disable'),
                        'value' => 0,
                    ],
                ],
            ]);
        }
    }

    /**
     * Return a custom exporter that includes all product attribute values.
     */
    public function getExporter(): ProductDataGridExport
    {
        return new ProductDataGridExport($this);
    }

    /**
     * Process request.
     */
    protected function processRequest(): void
    {
        if (
            core()->getConfigData('catalog.products.search.engine') != 'elastic'
            || core()->getConfigData('catalog.products.search.admin_mode') != 'elastic'
        ) {
            parent::processRequest();

            return;
        }

        /**
         * Store all request parameters in this variable; avoid using direct request helpers afterward.
         */
        $params = $this->validatedRequest();

        if (isset($params['export']) && (bool) $params['export']) {
            parent::processRequest();

            return;
        }

        $this->dispatchEvent('process_request.before', $this);

        $pagination = $params['pagination'];

        $channelCodes = request()->input('filters.channel') ?? core()->getAllChannels()->pluck('code')->toArray();

        $indexNames = collect($channelCodes)->map(function ($channelCode) {
            return Product::formatElasticSearchIndexName($channelCode, app()->getLocale());
        })->toArray();

        $results = ElasticSearch::search([
            'index' => $indexNames,
            'body' => [
                'from' => ($pagination['page'] * $pagination['per_page']) - $pagination['per_page'],
                'size' => $pagination['per_page'],
                'stored_fields' => [],
                'query' => [
                    'bool' => $this->getElasticFilters($params['filters'] ?? []) ?: new \stdClass,
                ],
                'sort' => $this->getElasticSort($params['sort'] ?? []),
                'track_total_hits' => true,
            ],
        ]);

        $ids = collect($results['hits']['hits'])->pluck('_id')->toArray();

        $this->queryBuilder
            ->whereIn('product_flat.product_id', $ids);

        if ($ids) {
            $this->queryBuilder
                ->orderBy(DB::raw('FIELD('.DB::getTablePrefix().'product_flat.product_id, '.implode(',', $ids).')'));
        }

        $total = $results['hits']['total']['value'];

        $this->paginator = new LengthAwarePaginator(
            $total ? $this->queryBuilder->get() : [],
            $total,
            $pagination['per_page'],
            $pagination['page'],
            [
                'path' => request()->url(),
                'query' => [],
            ]
        );

        $this->dispatchEvent('process_request.after', $this);
    }

    /**
     * Process request.
     */
    protected function getElasticFilters($params): array
    {
        $filters = [];

        foreach ($params as $attribute => $value) {
            if (in_array($attribute, ['channel', 'locale'])) {
                continue;
            }

            if ($attribute == 'all') {
                $attribute = 'name';
            }

            $filters['filter'][] = $this->getFilterValue($attribute, $value);
        }

        return $filters;
    }

    /**
     * Return applied filters
     */
    public function getFilterValue(mixed $attribute, mixed $values): array
    {
        switch ($attribute) {
            case 'product_id':
                return [
                    'terms' => [
                        'id' => $values,
                    ],
                ];

            case 'attribute_family':
                return [
                    'terms' => [
                        'attribute_family_id' => $values,
                    ],
                ];

            case 'sku':
            case 'name':
                $filters = [];

                foreach ($values as $value) {
                    $filters['bool']['should'][] = [
                        'match_phrase_prefix' => [
                            $attribute => $value,
                        ],
                    ];
                }

                return $filters;

            default:
                return [
                    'terms' => [
                        $attribute => $values,
                    ],
                ];
        }
    }

    /**
     * Process request.
     */
    protected function getElasticSort($params): array
    {
        $sort = $params['column'] ?? $this->primaryColumn;

        if ($sort == 'type') {
            $sort .= '.keyword';
        }

        if ($sort == 'name') {
            $sort .= '.keyword';
        }

        if ($sort == 'attribute_family') {
            $sort .= '_id';
        }

        if ($sort == 'product_id') {
            $sort = 'id';
        }

        return [
            $sort => [
                'order' => $params['order'] ?? $this->sortOrder,
            ],
        ];
    }
}
