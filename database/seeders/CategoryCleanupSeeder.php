<?php

namespace Database\Seeders;

use Frooxi\Category\Models\Category;
use Frooxi\Category\Models\CategoryTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryCleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rootCategory = Category::query()->find(1);

        if (! $rootCategory) {
            $this->command?->error('Root category with ID 1 was not found.');

            return;
        }

        $structure = $this->getCategoryStructure();
        $createdCount = 0;

        DB::transaction(function () use ($structure, &$createdCount) {
            CategoryTranslation::query()
                ->where('category_id', '>', 1)
                ->delete();

            Category::query()
                ->where('id', '>', 1)
                ->delete();

            Category::fixTree();

            foreach ($structure as $parentPosition => $parentCategory) {
                $parent = $this->createCategory(
                    parentId: 1,
                    position: $parentPosition + 1,
                    name: $parentCategory['name'],
                    slug: $parentCategory['slug'],
                    bnName: $parentCategory['bn_name'],
                    description: $parentCategory['description'],
                    bnDescription: $parentCategory['bn_description'],
                );

                $createdCount++;

                foreach ($parentCategory['children'] as $childPosition => $childCategory) {
                    $this->createCategory(
                        parentId: $parent->id,
                        position: $childPosition + 1,
                        name: $childCategory['name'],
                        slug: $childCategory['slug'],
                        bnName: $childCategory['bn_name'],
                        description: $childCategory['description'],
                        bnDescription: $childCategory['bn_description'],
                    );

                    $createdCount++;
                }
            }

            Category::fixTree();
        });

        $this->command?->info("Category cleanup complete. Created {$createdCount} categories under Root.");
    }

    /**
     * Create a category and insert its translations.
     */
    protected function createCategory(
        int $parentId,
        int $position,
        string $name,
        string $slug,
        string $bnName,
        string $description,
        string $bnDescription
    ): Category {
        $category = Category::query()->create([
            'parent_id' => $parentId,
            'position' => $position,
            'status' => 1,
            'display_mode' => 'products_and_description',
        ]);

        DB::table('category_translations')->insert([
            [
                'category_id' => $category->id,
                'locale' => 'en',
                'locale_id' => null,
                'name' => $name,
                'slug' => $slug,
                'url_path' => $slug,
                'description' => $description,
                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
            ],
            [
                'category_id' => $category->id,
                'locale' => 'bn',
                'locale_id' => null,
                'name' => $bnName,
                'slug' => $slug,
                'url_path' => $slug,
                'description' => $bnDescription,
                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
            ],
        ]);

        return $category;
    }

    /**
     * Get the target two-level category structure.
     */
    protected function getCategoryStructure(): array
    {
        return [
            [
                'name' => 'Women',
                'bn_name' => 'উইমেন',
                'slug' => 'women',
                'description' => '<p>Discover essential womenswear categories.</p>',
                'bn_description' => '<p>প্রয়োজনীয় নারীদের পোশাকের ক্যাটাগরি দেখুন।</p>',
                'children' => [
                    [
                        'name' => 'One Piece',
                        'bn_name' => 'ওয়ান পিস',
                        'slug' => 'women-one-piece',
                        'description' => '<p>Shop women\'s one piece.</p>',
                        'bn_description' => '<p>নারীদের ওয়ান পিস কিনুন।</p>',
                    ],
                    [
                        'name' => 'Two Piece',
                        'bn_name' => 'টু পিস',
                        'slug' => 'women-two-piece',
                        'description' => '<p>Shop women\'s two piece.</p>',
                        'bn_description' => '<p>নারীদের টু পিস কিনুন।</p>',
                    ],
                    [
                        'name' => 'Three Piece',
                        'bn_name' => 'থ্রি পিস',
                        'slug' => 'women-three-piece',
                        'description' => '<p>Shop women\'s three piece.</p>',
                        'bn_description' => '<p>নারীদের থ্রি পিস কিনুন।</p>',
                    ],
                ],
            ],
            [
                'name' => 'Men',
                'bn_name' => 'মেন',
                'slug' => 'men',
                'description' => '<p>Discover essential menswear categories.</p>',
                'bn_description' => '<p>প্রয়োজনীয় পুরুষদের পোশাকের ক্যাটাগরি দেখুন।</p>',
                'children' => [
                    [
                        'name' => 'Shirt',
                        'bn_name' => 'শার্ট',
                        'slug' => 'men-shirt',
                        'description' => '<p>Shop men\'s shirts.</p>',
                        'bn_description' => '<p>পুরুষদের শার্ট কিনুন।</p>',
                    ],
                    [
                        'name' => 'T-Shirt',
                        'bn_name' => 'টি-শার্ট',
                        'slug' => 'men-tshirt',
                        'description' => '<p>Shop men\'s t-shirts.</p>',
                        'bn_description' => '<p>পুরুষদের টি-শার্ট কিনুন।</p>',
                    ],
                    [
                        'name' => 'Blazer',
                        'bn_name' => 'ব্লেজার',
                        'slug' => 'men-blazer',
                        'description' => '<p>Shop men\'s blazers.</p>',
                        'bn_description' => '<p>পুরুষদের ব্লেজার কিনুন।</p>',
                    ],
                    [
                        'name' => 'Polo',
                        'bn_name' => 'পোলো',
                        'slug' => 'men-polo',
                        'description' => '<p>Shop men\'s polo styles.</p>',
                        'bn_description' => '<p>পুরুষদের পোলো স্টাইল কিনুন।</p>',
                    ],
                    [
                        'name' => 'Panjabi',
                        'bn_name' => 'পাঞ্জাবি',
                        'slug' => 'men-panjabi',
                        'description' => '<p>Shop men\'s panjabi.</p>',
                        'bn_description' => '<p>পুরুষদের পাঞ্জাবি কিনুন।</p>',
                    ],
                    [
                        'name' => 'Thobe',
                        'bn_name' => 'থোব',
                        'slug' => 'men-thobe',
                        'description' => '<p>Shop men\'s thobe.</p>',
                        'bn_description' => '<p>পুরুষদের থোব কিনুন।</p>',
                    ],
                    [
                        'name' => 'Kabli',
                        'bn_name' => 'কাবলি',
                        'slug' => 'men-kabli',
                        'description' => '<p>Shop men\'s kabli.</p>',
                        'bn_description' => '<p>পুরুষদের কাবলি কিনুন।</p>',
                    ],
                    [
                        'name' => 'Sherwani',
                        'bn_name' => 'শেরওয়ানি',
                        'slug' => 'men-sherwani',
                        'description' => '<p>Shop men\'s sherwani.</p>',
                        'bn_description' => '<p>পুরুষদের শেরওয়ানি কিনুন।</p>',
                    ],
                    [
                        'name' => 'Jeans',
                        'bn_name' => 'জিন্স',
                        'slug' => 'men-jeans',
                        'description' => '<p>Shop men\'s jeans.</p>',
                        'bn_description' => '<p>পুরুষদের জিন্স কিনুন।</p>',
                    ],
                    [
                        'name' => 'Trousers',
                        'bn_name' => 'ট্রাউজার্স',
                        'slug' => 'men-trousers',
                        'description' => '<p>Shop men\'s trousers.</p>',
                        'bn_description' => '<p>পুরুষদের ট্রাউজার্স কিনুন।</p>',
                    ],
                ],
            ],
            [
                'name' => 'Kids',
                'bn_name' => 'কিডস',
                'slug' => 'kids',
                'description' => '<p>Explore practical everyday styles for kids.</p>',
                'bn_description' => '<p>বাচ্চাদের জন্য ব্যবহারিক দৈনন্দিন স্টাইল দেখুন।</p>',
                'children' => [
                    [
                        'name' => 'Boys',
                        'bn_name' => 'ছেলেদের',
                        'slug' => 'kids-boys',
                        'description' => '<p>Shop clothing for boys.</p>',
                        'bn_description' => '<p>ছেলেদের পোশাক কিনুন।</p>',
                    ],
                    [
                        'name' => 'Girls',
                        'bn_name' => 'মেয়েদের',
                        'slug' => 'kids-girls',
                        'description' => '<p>Shop clothing for girls.</p>',
                        'bn_description' => '<p>মেয়েদের পোশাক কিনুন।</p>',
                    ],
                ],
            ],
        ];
    }
}
