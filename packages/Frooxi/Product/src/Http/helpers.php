<?php

use Frooxi\Product\Facades\ProductImage;
use Frooxi\Product\Facades\ProductVideo;
use Frooxi\Product\Helpers\Toolbar;

if (! function_exists('product_image')) {
    /**
     * Product image helper.
     *
     * @return Frooxi\Product\ProductImage
     */
    function product_image()
    {
        return ProductImage::getFacadeRoot();
    }
}

if (! function_exists('product_video')) {
    /**
     * Product video helper.
     *
     * @return Frooxi\Product\ProductVideo
     */
    function product_video()
    {
        return ProductVideo::getFacadeRoot();
    }
}

if (! function_exists('product_toolbar')) {
    /**
     * Product tolbar helper.
     *
     * @return Toolbar
     */
    function product_toolbar()
    {
        return app()->make(Toolbar::class);
    }
}
