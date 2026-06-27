<?php

namespace Frooxi\Admin\DataGrids\Storefront;

use Frooxi\Admin\DataGrids\Catalog\ProductDataGrid;
use Illuminate\Database\Query\Builder;

class FlashSaleProductDataGrid extends ProductDataGrid
{
    /**
     * Prepare query builder.
     *
     * @return Builder
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = parent::prepareQueryBuilder();

        $queryBuilder->where('products.flash_sale_discount', '>', 0)
            ->addSelect('products.flash_sale_discount as flash_sale_discount');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        // 1. Image
        $this->addColumn([
            'index' => 'base_image',
            'label' => trans('admin::app.catalog.products.index.datagrid.image'),
            'type' => 'string',
            'exportable' => false,
            'closure' => function ($row) {
                if (! $row->base_image) {
                    return '';
                }

                if (config('filesystems.default') === 'cloudinary') {
                    $url = cloudinary_url($row->base_image, ['w' => 100, 'q' => 'auto']);
                } else {
                    $url = \Illuminate\Support\Facades\Storage::url($row->base_image);
                }

                return '<img src="' . $url . '" style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px;">';
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

        // 3. Price
        $this->addColumn([
            'index' => 'price',
            'label' => trans('admin::app.catalog.products.index.datagrid.price'),
            'type' => 'decimal',
            'filterable' => true,
            'sortable' => true,
            'closure' => function ($row) {
                return core()->formatPrice((float) ($row->price ?? 0), core()->getCurrentCurrencyCode());
            },
        ]);

        // 4. Discount Percentage
        $this->addColumn([
            'index' => 'flash_sale_discount',
            'label' => 'Discount (%)',
            'type' => 'integer',
            'searchable' => false,
            'filterable' => true,
            'sortable' => true,
            'closure' => function ($row) {
                return $row->flash_sale_discount.'%';
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
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        if (bouncer()->hasPermission('catalog.products.edit')) {
            $this->addAction([
                'icon' => 'icon-edit',
                'title' => trans('admin::app.catalog.products.index.datagrid.edit'),
                'method' => 'GET',
                'url' => function ($row) {
                    return route('admin.catalog.products.edit', [
                        'id' => $row->product_id,
                        'flash_sale' => 1,
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
}
