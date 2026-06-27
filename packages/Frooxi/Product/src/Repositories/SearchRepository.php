<?php

namespace Frooxi\Product\Repositories;

use Frooxi\Core\Traits\Sanitizer;
use Illuminate\Support\Facades\Storage;

class SearchRepository extends ProductRepository
{
    use Sanitizer;

    /**
     * Upload provided image
     *
     * @param  array  $data
     * @return string
     */
    public function uploadSearchImage($data)
    {
        $path = request()->file('image')->store('product-search');

        $this->sanitizeSVG($path, $data['image']->getMimeType());

        return Storage::url($path);
    }
}
