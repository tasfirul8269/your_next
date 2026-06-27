<?php

namespace Frooxi\Category\Observers;

use Frooxi\Category\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CategoryObserver
{
    /**
     * Handle the Category "deleted" event.
     *
     * @param  \Frooxi\Category\Contracts\Category  $category
     * @return void
     */
    public function deleted($category)
    {
        Cache::increment('category_tree_version');
        Cache::forget('shop_categories_tree');

        Storage::deleteDirectory('category/'.$category->id);
    }

    /**
     * Handle the Category "saved" event.
     *
     * @param  \Frooxi\Category\Contracts\Category  $category
     * @return void
     */
    public function saved($category)
    {
        Cache::increment('category_tree_version');
        Cache::forget('shop_categories_tree');

        foreach ($category->children as $child) {
            $child->touch();
        }
    }
}
