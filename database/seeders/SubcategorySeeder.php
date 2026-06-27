<?php

namespace Database\Seeders;

use Frooxi\Category\Models\Category;
use Frooxi\Category\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $subcategories = [
            // Women (ID: 2)
            2 => [
                ['name' => 'Tops & Blouses',  'slug' => 'women-tops-blouses'],
                ['name' => 'Dresses',          'slug' => 'women-dresses'],
                ['name' => 'Trousers & Jeans', 'slug' => 'women-trousers-jeans'],
                ['name' => 'Ethnic Wear',      'slug' => 'women-ethnic-wear'],
            ],
            // Men (ID: 3)
            3 => [
                ['name' => 'T-Shirts',         'slug' => 'men-tshirts'],
                ['name' => 'Shirts',           'slug' => 'men-shirts'],
                ['name' => 'Trousers & Jeans', 'slug' => 'men-trousers-jeans'],
                ['name' => 'Ethnic Wear',      'slug' => 'men-ethnic-wear'],
            ],
            // Kids (ID: 5)
            5 => [
                ['name' => 'Boys',   'slug' => 'kids-boys'],
                ['name' => 'Girls',  'slug' => 'kids-girls'],
                ['name' => 'Infants', 'slug' => 'kids-infants'],
            ],
        ];

        $locales = ['en', 'bn', 'ar', 'ca', 'de', 'es', 'fa', 'fr', 'he', 'hi_IN',
            'id', 'it', 'ja', 'nl', 'pl', 'pt_BR', 'ru', 'sin', 'tr', 'uk', 'zh_CN'];

        foreach ($subcategories as $parentId => $children) {
            foreach ($children as $child) {
                $exists = CategoryTranslation::where('slug', $child['slug'])->exists();

                if ($exists) {
                    $this->command->info("Skipping existing: {$child['slug']}");

                    continue;
                }

                $category = Category::create([
                    'parent_id' => $parentId,
                    'position' => 0,
                    'status' => 1,
                    'display_mode' => 'products_only',
                ]);

                foreach ($locales as $locale) {
                    CategoryTranslation::updateOrCreate(
                        ['category_id' => $category->id, 'locale' => $locale],
                        [
                            'name' => $child['name'],
                            'slug' => $child['slug'],
                            'meta_title' => $child['name'],
                            'description' => null,
                            'meta_description' => null,
                            'meta_keywords' => null,
                        ]
                    );
                }

                $this->command->info("Created: {$child['name']} under parent {$parentId}");
            }
        }

        $this->command->info('Done! Subcategories created.');
    }
}
