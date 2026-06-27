<?php

namespace Frooxi\Shop\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CategoryTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Collect IDs of this category + all its descendants so we can
        // count every product that lives anywhere in the subtree.
        $subtreeIds = $this->descendants()->pluck('id')->push($this->id);

        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => $this->url,
            'status' => $this->status,
            'logo_url' => $this->logo_url,
            'banner_url' => $this->banner_url,
            'products_count' => DB::table('product_categories')
                ->whereIn('category_id', $subtreeIds)
                ->distinct('product_id')
                ->count('product_id'),
            'children' => self::collection($this->children),
        ];
    }
}
