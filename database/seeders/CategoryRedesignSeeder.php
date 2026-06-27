<?php

namespace Database\Seeders;

use Frooxi\Category\Models\Category;
use Frooxi\Category\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategoryRedesignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the root category
        $rootCategory = Category::first(); // ID: 1

        if (! $rootCategory) {
            $this->command->error('Root category not found!');

            return;
        }

        // Define the complete category structure for a clothing brand
        $categories = [
            'Men' => [
                'slug' => 'men',
                'children' => [
                    'T-Shirts' => [
                        'slug' => 'men-tshirts',
                        'children' => [
                            'Crew Neck' => 'men-tshirts-crew-neck',
                            'V-Neck' => 'men-tshirts-v-neck',
                            'Polo' => 'men-tshirts-polo',
                            'Henley' => 'men-tshirts-henley',
                            'Long Sleeve' => 'men-tshirts-long-sleeve',
                        ],
                    ],
                    'Shirts' => [
                        'slug' => 'men-shirts',
                        'children' => [
                            'Casual Shirts' => 'men-shirts-casual',
                            'Formal Shirts' => 'men-shirts-formal',
                            'Denim Shirts' => 'men-shirts-denim',
                            'Linen Shirts' => 'men-shirts-linen',
                            'Flannel Shirts' => 'men-shirts-flannel',
                        ],
                    ],
                    'Trousers' => [
                        'slug' => 'men-trousers',
                        'children' => [
                            'Chinos' => 'men-trousers-chinos',
                            'Formal Trousers' => 'men-trousers-formal',
                            'Cargo Pants' => 'men-trousers-cargo',
                            'Joggers' => 'men-trousers-joggers',
                        ],
                    ],
                    'Jeans' => [
                        'slug' => 'men-jeans',
                        'children' => [
                            'Slim Fit' => 'men-jeans-slim-fit',
                            'Regular Fit' => 'men-jeans-regular-fit',
                            'Relaxed Fit' => 'men-jeans-relaxed-fit',
                            'Skinny' => 'men-jeans-skinny',
                            'Straight Leg' => 'men-jeans-straight',
                        ],
                    ],
                    'Jackets' => [
                        'slug' => 'men-jackets',
                        'children' => [
                            'Leather Jackets' => 'men-jackets-leather',
                            'Denim Jackets' => 'men-jackets-denim',
                            'Bomber Jackets' => 'men-jackets-bomber',
                            'Windbreakers' => 'men-jackets-windbreakers',
                            'Parkas' => 'men-jackets-parkas',
                        ],
                    ],
                    'Suits' => [
                        'slug' => 'men-suits',
                        'children' => [
                            'Two-Piece Suits' => 'men-suits-two-piece',
                            'Three-Piece Suits' => 'men-suits-three-piece',
                            'Tuxedos' => 'men-suits-tuxedos',
                            'Blazers' => 'men-suits-blazers',
                        ],
                    ],
                ],
            ],
            'Women' => [
                'slug' => 'women',
                'children' => [
                    'Dresses' => [
                        'slug' => 'women-dresses',
                        'children' => [
                            'Casual Dresses' => 'women-dresses-casual',
                            'Maxi Dresses' => 'women-dresses-maxi',
                            'Mini Dresses' => 'women-dresses-mini',
                            'Midi Dresses' => 'women-dresses-midi',
                            'Cocktail Dresses' => 'women-dresses-cocktail',
                            'Evening Gowns' => 'women-dresses-evening',
                        ],
                    ],
                    'Tops' => [
                        'slug' => 'women-tops',
                        'children' => [
                            'T-Shirts' => 'women-tops-tshirts',
                            'Blouses' => 'women-tops-blouses',
                            'Tank Tops' => 'women-tops-tank',
                            'Crop Tops' => 'women-tops-crop',
                            'Camisoles' => 'women-tops-camisoles',
                        ],
                    ],
                    'Jeans' => [
                        'slug' => 'women-jeans',
                        'children' => [
                            'Skinny Jeans' => 'women-jeans-skinny',
                            'Straight Jeans' => 'women-jeans-straight',
                            'Bootcut Jeans' => 'women-jeans-bootcut',
                            'Wide Leg Jeans' => 'women-jeans-wide-leg',
                            'High Rise Jeans' => 'women-jeans-high-rise',
                        ],
                    ],
                    'Skirts' => [
                        'slug' => 'women-skirts',
                        'children' => [
                            'Mini Skirts' => 'women-skirts-mini',
                            'Midi Skirts' => 'women-skirts-midi',
                            'Maxi Skirts' => 'women-skirts-maxi',
                            'Pencil Skirts' => 'women-skirts-pencil',
                            'A-Line Skirts' => 'women-skirts-a-line',
                        ],
                    ],
                    'Jackets' => [
                        'slug' => 'women-jackets',
                        'children' => [
                            'Leather Jackets' => 'women-jackets-leather',
                            'Denim Jackets' => 'women-jackets-denim',
                            'Blazers' => 'women-jackets-blazers',
                            'Bomber Jackets' => 'women-jackets-bomber',
                            'Trench Coats' => 'women-jackets-trench',
                        ],
                    ],
                ],
            ],
            'Kids' => [
                'slug' => 'kids',
                'children' => [
                    'Boys' => [
                        'slug' => 'kids-boys',
                        'children' => [
                            'Boys T-Shirts' => 'kids-boys-tshirts',
                            'Boys Shirts' => 'kids-boys-shirts',
                            'Boys Jeans' => 'kids-boys-jeans',
                            'Boys Shorts' => 'kids-boys-shorts',
                            'Boys Jackets' => 'kids-boys-jackets',
                        ],
                    ],
                    'Girls' => [
                        'slug' => 'kids-girls',
                        'children' => [
                            'Girls Dresses' => 'kids-girls-dresses',
                            'Girls Tops' => 'kids-girls-tops',
                            'Girls Skirts' => 'kids-girls-skirts',
                            'Girls Leggings' => 'kids-girls-leggings',
                            'Girls Jackets' => 'kids-girls-jackets',
                        ],
                    ],
                    'Baby Boys' => [
                        'slug' => 'kids-baby-boys',
                        'children' => [
                            'Baby Boy Rompers' => 'kids-baby-boys-rompers',
                            'Baby Boy Bodysuits' => 'kids-baby-boys-bodysuits',
                            'Baby Boy Sleepwear' => 'kids-baby-boys-sleepwear',
                        ],
                    ],
                    'Baby Girls' => [
                        'slug' => 'kids-baby-girls',
                        'children' => [
                            'Baby Girl Dresses' => 'kids-baby-girls-dresses',
                            'Baby Girl Rompers' => 'kids-baby-girls-rompers',
                            'Baby Girl Bodysuits' => 'kids-baby-girls-bodysuits',
                        ],
                    ],
                ],
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($categories as $parentName => $parentData) {
            // Find or create parent category
            $parentCategory = Category::whereHas('translations', function ($query) use ($parentName) {
                $query->where('name', $parentName);
            })->first();

            if (! $parentCategory) {
                // Create parent category
                $parentCategory = Category::create([
                    'parent_id' => $rootCategory->id,
                    'position' => 0,
                    'status' => 1,
                    'display_mode' => 'products_and_description',
                ]);

                // Add translations
                foreach (['en', 'bn'] as $locale) {
                    CategoryTranslation::updateOrCreate(
                        ['category_id' => $parentCategory->id, 'locale' => $locale],
                        [
                            'name' => $parentName,
                            'slug' => $parentData['slug'],
                            'description' => "<p>Shop our collection of {$parentName}'s clothing</p>",
                        ]
                    );
                }

                $created++;
                $this->command->info("✓ Created parent category: {$parentName}");
            } else {
                $this->command->info("✓ Found parent category: {$parentName}");
            }

            // Create or update child categories
            foreach ($parentData['children'] as $childName => $childData) {
                // Handle both old format (string) and new format (array with children)
                if (is_string($childData)) {
                    $childSlug = $childData;
                    $grandchildren = [];
                } else {
                    $childSlug = $childData['slug'];
                    $grandchildren = $childData['children'] ?? [];
                }

                $childCategory = Category::whereHas('translations', function ($query) use ($childSlug) {
                    $query->where('slug', $childSlug);
                })->first();

                if (! $childCategory) {
                    // Create child category
                    $childCategory = Category::create([
                        'parent_id' => $parentCategory->id,
                        'position' => 0,
                        'status' => 1,
                        'display_mode' => 'products_and_description',
                    ]);

                    // Add translations
                    foreach (['en', 'bn'] as $locale) {
                        CategoryTranslation::updateOrCreate(
                            ['category_id' => $childCategory->id, 'locale' => $locale],
                            [
                                'name' => $childName,
                                'slug' => $childSlug,
                                'description' => "<p>Shop {$childName} collection</p>",
                            ]
                        );
                    }

                    $created++;
                    $this->command->info("  ✓ Created: {$childName}");
                } else {
                    $updated++;
                    $this->command->info("  ✓ Exists: {$childName}");
                }

                // Create grandchildren (third level)
                foreach ($grandchildren as $grandchildName => $grandchildSlug) {
                    $grandchildCategory = Category::whereHas('translations', function ($query) use ($grandchildSlug) {
                        $query->where('slug', $grandchildSlug);
                    })->first();

                    if (! $grandchildCategory) {
                        // Create grandchild category
                        $grandchildCategory = Category::create([
                            'parent_id' => $childCategory->id,
                            'position' => 0,
                            'status' => 1,
                            'display_mode' => 'products_and_description',
                        ]);

                        // Add translations
                        foreach (['en', 'bn'] as $locale) {
                            CategoryTranslation::updateOrCreate(
                                ['category_id' => $grandchildCategory->id, 'locale' => $locale],
                                [
                                    'name' => $grandchildName,
                                    'slug' => $grandchildSlug,
                                    'description' => "<p>Shop {$grandchildName} collection</p>",
                                ]
                            );
                        }

                        $created++;
                        $this->command->info("    ✓ Created: {$grandchildName}");
                    } else {
                        $updated++;
                        $this->command->info("    ✓ Exists: {$grandchildName}");
                    }
                }
            }
        }

        $this->command->info("\n✨ Category structure complete!");
        $this->command->info("📊 Created: {$created} | Updated: {$updated}");
    }
}
