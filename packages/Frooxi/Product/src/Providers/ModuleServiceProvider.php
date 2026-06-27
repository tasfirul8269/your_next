<?php

namespace Frooxi\Product\Providers;

use Frooxi\Core\Providers\CoreModuleServiceProvider;
use Frooxi\Product\Models\Product;
use Frooxi\Product\Models\ProductAttributeValue;
use Frooxi\Product\Models\ProductBundleOption;
use Frooxi\Product\Models\ProductBundleOptionProduct;
use Frooxi\Product\Models\ProductBundleOptionTranslation;
use Frooxi\Product\Models\ProductCustomerGroupPrice;
use Frooxi\Product\Models\ProductCustomizableOption;
use Frooxi\Product\Models\ProductCustomizableOptionPrice;
use Frooxi\Product\Models\ProductCustomizableOptionTranslation;
use Frooxi\Product\Models\ProductDownloadableLink;
use Frooxi\Product\Models\ProductDownloadableSample;
use Frooxi\Product\Models\ProductFlat;
use Frooxi\Product\Models\ProductGroupedProduct;
use Frooxi\Product\Models\ProductImage;
use Frooxi\Product\Models\ProductInventory;
use Frooxi\Product\Models\ProductInventoryIndex;
use Frooxi\Product\Models\ProductOrderedInventory;
use Frooxi\Product\Models\ProductPriceIndex;
use Frooxi\Product\Models\ProductReview;
use Frooxi\Product\Models\ProductReviewAttachment;
use Frooxi\Product\Models\ProductSalableInventory;
use Frooxi\Product\Models\ProductVideo;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        Product::class,
        ProductAttributeValue::class,
        ProductBundleOption::class,
        ProductBundleOptionProduct::class,
        ProductBundleOptionTranslation::class,
        ProductCustomerGroupPrice::class,
        ProductCustomizableOption::class,
        ProductCustomizableOptionPrice::class,
        ProductCustomizableOptionTranslation::class,
        ProductDownloadableLink::class,
        ProductDownloadableSample::class,
        ProductFlat::class,
        ProductGroupedProduct::class,
        ProductImage::class,
        ProductInventory::class,
        ProductInventoryIndex::class,
        ProductOrderedInventory::class,
        ProductPriceIndex::class,
        ProductReview::class,
        ProductReviewAttachment::class,
        ProductSalableInventory::class,
        ProductVideo::class,
    ];
}
