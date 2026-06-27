<?php

namespace Frooxi\Product;

use Frooxi\Product\Contracts\Product;

class ProductVideo
{
    /**
     * Retrieve collection of videos
     *
     * @param  Product  $product
     * @return array
     */
    public function getVideos($product)
    {
        if (! $product) {
            return [];
        }

        $videos = [];

        foreach ($product->videos as $video) {
            if (! $video->path) {
                continue;
            }

            $videos[] = [
                'type' => $video->type,
                'video_url' => cloudinary_url($video->path),
            ];
        }

        return $videos;
    }
}
