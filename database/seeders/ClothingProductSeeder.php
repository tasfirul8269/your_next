<?php

namespace Database\Seeders;

use Frooxi\Attribute\Models\Attribute;
use Frooxi\Attribute\Models\AttributeFamily;
use Frooxi\Attribute\Models\AttributeOption;
use Frooxi\Attribute\Repositories\AttributeRepository;
use Frooxi\Category\Models\CategoryTranslation;
use Frooxi\Product\Helpers\Indexers\Flat as FlatIndexer;
use Frooxi\Product\Helpers\Indexers\Inventory as InventoryIndexer;
use Frooxi\Product\Helpers\Indexers\Price as PriceIndexer;
use Frooxi\Product\Models\Product;
use Frooxi\Product\Repositories\ProductAttributeValueRepository;
use Frooxi\Product\Repositories\ProductInventoryRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClothingProductSeeder extends Seeder
{
    protected $customColorIds = [];

    protected $sizeOptions = [];

    protected $categoryMap = [];

    protected $urlKeyCounter = 1000;

    protected $attributeValueRepository;

    protected $inventoryRepository;

    protected $familyAttributes;

    protected $clothingColors = [
        'Midnight Black' => '#1A1A2E',
        'Ivory White' => '#FAF9F6',
        'Navy Blue' => '#1B3B6F',
        'Charcoal Grey' => '#36454F',
        'Burgundy' => '#800020',
        'Olive Green' => '#556B2F',
        'Dusty Rose' => '#DCAE96',
        'Sage Green' => '#9CAF88',
        'Sky Blue' => '#87CEEB',
        'Camel' => '#C19A6B',
        'Rust Orange' => '#B7410E',
        'Mustard Yellow' => '#FFDB58',
        'Lavender' => '#B57EDC',
        'Teal' => '#008080',
        'Cream' => '#FFFDD0',
    ];

    public function run(): void
    {
        $this->attributeValueRepository = app(ProductAttributeValueRepository::class);
        $this->inventoryRepository = app(ProductInventoryRepository::class);

        $this->createCustomColorOptions();
        $this->loadSizeOptions();
        $this->loadCategories();
        $this->loadFamilyAttributes();

        $created = 0;

        // ═══════════════════════════════════════════════════
        //  CONFIGURABLE PRODUCTS (only a few)
        // ═══════════════════════════════════════════════════

        $configurableProducts = [
            ['name' => 'Classic Crew Neck Tee', 'sku' => 'CF-MEN-TEE-CREW', 'short_description' => 'A timeless crew neck t-shirt crafted from premium combed cotton for all-day comfort.', 'description' => '<p>Our Classic Crew Neck Tee is the foundation of every wardrobe. Made from 100% premium combed cotton with a relaxed fit that drapes naturally.</p>', 'price' => 29.99, 'weight' => 0.2, 'category_slug' => 'men-tshirts', 'color_options' => ['Midnight Black', 'Ivory White', 'Navy Blue'], 'size_options' => ['S', 'M', 'L', 'XL', 'XXL'], 'new' => true, 'featured' => true],
            ['name' => 'Wrap Midi Dress', 'sku' => 'CF-WMN-DRS-WRAP', 'short_description' => 'A flattering wrap midi dress in fluid jersey — day-to-dinner versatility.', 'description' => '<p>Our Wrap Midi Dress is the definition of effortless chic. The wrap silhouette creates a beautiful V-neckline and cinches at the waist.</p>', 'price' => 79.99, 'weight' => 0.3, 'category_slug' => 'women-dresses', 'color_options' => ['Midnight Black', 'Burgundy'], 'size_options' => ['S', 'M', 'L', 'XL'], 'new' => true, 'featured' => true],
            ['name' => 'Anarkali Kurta Set', 'sku' => 'CF-WMN-ETHN-ANARKALI', 'short_description' => 'A flowing Anarkali kurta with churidar pants and dupatta.', 'description' => '<p>Make every occasion special with our Anarkali Kurta Set. The floor-length flared kurta features delicate print and embroidery details.</p>', 'price' => 99.99, 'weight' => 0.5, 'category_slug' => 'women-ethnic-wear', 'color_options' => ['Burgundy', 'Lavender'], 'size_options' => ['S', 'M', 'L', 'XL'], 'new' => true, 'featured' => true],
            ['name' => 'Premium Panjabi', 'sku' => 'CF-MEN-ETHN-PANJABI', 'short_description' => 'An elegantly embroidered Panjabi crafted from premium cotton blend fabric.', 'description' => '<p>Our Premium Panjabi combines traditional craftsmanship with contemporary style. Featuring intricate embroidery on the collar and placket.</p>', 'price' => 59.99, 'weight' => 0.35, 'category_slug' => 'men-ethnic-wear', 'color_options' => ['Ivory White', 'Sky Blue'], 'size_options' => ['S', 'M', 'L', 'XL'], 'new' => true, 'featured' => true],
        ];

        // ═══════════════════════════════════════════════════
        //  SIMPLE PRODUCTS (the majority)
        // ═══════════════════════════════════════════════════

        $simpleProducts = [
            // Men's T-Shirts
            ['name' => 'V-Neck Essential Tee', 'sku' => 'SP-MEN-VNECK-BLK', 'short_description' => 'A refined v-neck tee in soft-washed cotton for an effortless casual look.', 'description' => '<p>Upgrade your basics with our V-Neck Essential Tee. The subtle v-neckline adds a touch of sophistication.</p>', 'price' => 32.99, 'weight' => 0.2, 'category_slug' => 'men-tshirts', 'color_name' => 'Charcoal Grey', 'size_name' => 'M', 'new' => true, 'featured' => false],
            ['name' => 'Polo Sport Shirt', 'sku' => 'SP-MEN-POLO-NVY', 'short_description' => 'A classic piqué polo shirt with embroidered detail and ribbed collar.', 'description' => '<p>Our Polo Sport Shirt combines athletic heritage with modern tailoring. Crafted from premium cotton piqué.</p>', 'price' => 49.99, 'weight' => 0.25, 'category_slug' => 'men-tshirts', 'color_name' => 'Navy Blue', 'size_name' => 'L', 'new' => false, 'featured' => true],
            ['name' => 'Graphic Print Tee', 'sku' => 'SP-MEN-GFX-GRY', 'short_description' => 'A bold graphic print t-shirt in soft cotton jersey.', 'description' => '<p>Make a statement with our Graphic Print Tee. Premium cotton jersey with a bold front graphic.</p>', 'price' => 27.99, 'weight' => 0.18, 'category_slug' => 'men-tshirts', 'color_name' => 'Charcoal Grey', 'size_name' => 'L', 'new' => true, 'featured' => false],
            ['name' => 'Striped Crew Tee', 'sku' => 'SP-MEN-STRP-NVY', 'short_description' => 'A nautical-inspired striped crew neck tee in breathable cotton.', 'description' => '<p>Channel coastal vibes with our Striped Crew Tee. Classic nautical stripes on premium cotton.</p>', 'price' => 34.99, 'weight' => 0.2, 'category_slug' => 'men-tshirts', 'color_name' => 'Navy Blue', 'size_name' => 'M', 'new' => false, 'featured' => false],

            // Men's Shirts
            ['name' => 'Oxford Button-Down Shirt', 'sku' => 'SP-MEN-OXFORD-WHT', 'short_description' => 'A wardrobe staple — our Oxford button-down in premium brushed cotton.', 'description' => '<p>The Oxford Button-Down Shirt is a timeless classic reimagined with modern proportions.</p>', 'price' => 69.99, 'weight' => 0.3, 'category_slug' => 'men-shirts', 'color_name' => 'Ivory White', 'size_name' => 'M', 'new' => true, 'featured' => true],
            ['name' => 'Slim Fit Dress Shirt', 'sku' => 'SP-MEN-DRESS-SKY', 'short_description' => 'A sharply tailored slim-fit dress shirt for the modern professional.', 'description' => '<p>Our Slim Fit Dress Shirt is cut closer to the body for a streamlined silhouette.</p>', 'price' => 79.99, 'weight' => 0.28, 'category_slug' => 'men-shirts', 'color_name' => 'Sky Blue', 'size_name' => 'L', 'new' => false, 'featured' => false],
            ['name' => 'Linen Camp Collar Shirt', 'sku' => 'SP-MEN-LINEN-CRM', 'short_description' => 'A breezy linen shirt with a relaxed camp collar for warm-weather style.', 'description' => '<p>Embrace the ease of summer with our Linen Camp Collar Shirt. Crafted from 100% European flax linen.</p>', 'price' => 89.99, 'weight' => 0.25, 'category_slug' => 'men-shirts', 'color_name' => 'Cream', 'size_name' => 'M', 'new' => true, 'featured' => true],
            ['name' => 'Flannel Check Shirt', 'sku' => 'SP-MEN-FLNL-GRY', 'short_description' => 'A cozy flannel check shirt in brushed cotton for cooler days.', 'description' => '<p>Our Flannel Check Shirt is the perfect layering piece. Brushed cotton flannel with a timeless check pattern.</p>', 'price' => 59.99, 'weight' => 0.3, 'category_slug' => 'men-shirts', 'color_name' => 'Charcoal Grey', 'size_name' => 'L', 'new' => false, 'featured' => false],

            // Men's Trousers & Jeans
            ['name' => 'Slim Fit Stretch Jeans', 'sku' => 'SP-MEN-JEANS-BLK', 'short_description' => 'Modern slim-fit jeans with added stretch for unrestricted movement.', 'description' => '<p>Our Slim Fit Stretch Jeans are designed for the guy on the move. Premium selvedge denim with 2% elastane.</p>', 'price' => 89.99, 'weight' => 0.6, 'category_slug' => 'men-trousers-jeans', 'color_name' => 'Midnight Black', 'size_name' => 'M', 'new' => true, 'featured' => true],
            ['name' => 'Classic Chino Trousers', 'sku' => 'SP-MEN-CHINO-CML', 'short_description' => 'Versatile chinos in premium stretch cotton twill — dress them up or down.', 'description' => '<p>The Classic Chino is our most versatile trouser. Made from premium stretch cotton twill.</p>', 'price' => 64.99, 'weight' => 0.45, 'category_slug' => 'men-trousers-jeans', 'color_name' => 'Camel', 'size_name' => 'M', 'new' => false, 'featured' => false],
            ['name' => 'Jogger Sweatpants', 'sku' => 'SP-MEN-JOGGR-GRY', 'short_description' => 'Ultra-soft fleece joggers with tapered fit and zippered pockets.', 'description' => '<p>Our Jogger Sweatpants redefine comfort. Made from heavyweight brushed fleece with a tapered silhouette.</p>', 'price' => 54.99, 'weight' => 0.4, 'category_slug' => 'men-trousers-jeans', 'color_name' => 'Charcoal Grey', 'size_name' => 'L', 'new' => true, 'featured' => false],
            ['name' => 'Formal Wool Trousers', 'sku' => 'SP-MEN-FORMAL-BLK', 'short_description' => 'Sharply tailored wool-blend trousers for business and formal occasions.', 'description' => '<p>Our Formal Wool Trousers are crafted from a premium wool blend with a sharp crease and a tailored fit.</p>', 'price' => 99.99, 'weight' => 0.45, 'category_slug' => 'men-trousers-jeans', 'color_name' => 'Midnight Black', 'size_name' => 'M', 'new' => false, 'featured' => true],

            // Men's Ethnic Wear
            ['name' => 'Silk Kurta Set', 'sku' => 'SP-MEN-KURTA-CRM', 'short_description' => 'A luxurious silk kurta with matching pants for elegant occasions.', 'description' => '<p>Elevate your ethnic wardrobe with our Silk Kurta Set. Crafted from art silk with a subtle sheen.</p>', 'price' => 99.99, 'weight' => 0.4, 'category_slug' => 'men-ethnic-wear', 'color_name' => 'Cream', 'size_name' => 'L', 'new' => false, 'featured' => true],
            ['name' => 'Cotton Panjabi', 'sku' => 'SP-MEN-PANJ-WHT', 'short_description' => 'A clean cotton Panjabi with subtle tonal embroidery for everyday elegance.', 'description' => '<p>Our Cotton Panjabi offers everyday elegance with clean lines and subtle tonal embroidery.</p>', 'price' => 44.99, 'weight' => 0.3, 'category_slug' => 'men-ethnic-wear', 'color_name' => 'Ivory White', 'size_name' => 'M', 'new' => true, 'featured' => false],

            // Women's Tops & Blouses
            ['name' => 'Relaxed Fit Blouse', 'sku' => 'SP-WMN-BLOUSE-WHT', 'short_description' => 'A flowy relaxed-fit blouse in lightweight crepe with elegant draping.', 'description' => '<p>Our Relaxed Fit Blouse is all about effortless elegance. Crafted from lightweight crepe that drapes beautifully.</p>', 'price' => 49.99, 'weight' => 0.18, 'category_slug' => 'women-tops-blouses', 'color_name' => 'Ivory White', 'size_name' => 'M', 'new' => true, 'featured' => true],
            ['name' => 'Fitted Crop Top', 'sku' => 'SP-WMN-CROP-BLK', 'short_description' => 'A sleek crop top with structured bodice and modern square neckline.', 'description' => '<p>Make a statement with our Fitted Crop Top. The structured bodice and square neckline create a flattering silhouette.</p>', 'price' => 34.99, 'weight' => 0.12, 'category_slug' => 'women-tops-blouses', 'color_name' => 'Midnight Black', 'size_name' => 'S', 'new' => true, 'featured' => false],
            ['name' => 'Silk Camisole', 'sku' => 'SP-WMN-CAMI-BRG', 'short_description' => 'A luxurious silk camisole with delicate lace trim and adjustable straps.', 'description' => '<p>Indulge in the luxury of our Silk Camisole. Made from pure mulberry silk with delicate lace trim.</p>', 'price' => 59.99, 'weight' => 0.1, 'category_slug' => 'women-tops-blouses', 'color_name' => 'Burgundy', 'size_name' => 'M', 'new' => false, 'featured' => true],
            ['name' => 'Dusty Rose Peplum Top', 'sku' => 'SP-WMN-PEPL-DRS', 'short_description' => 'A feminine peplum top in soft dusty rose with flattering silhouette.', 'description' => '<p>Our Peplum Top adds a feminine touch to any outfit. The fitted bodice flares into a peplum hem.</p>', 'price' => 44.99, 'weight' => 0.15, 'category_slug' => 'women-tops-blouses', 'color_name' => 'Dusty Rose', 'size_name' => 'M', 'new' => true, 'featured' => false],

            // Women's Dresses
            ['name' => 'Floral Maxi Dress', 'sku' => 'SP-WMN-MAXI-GRN', 'short_description' => 'A flowing floral maxi dress with smocked bodice and tiered skirt.', 'description' => '<p>Embrace your romantic side with our Floral Maxi Dress. The smocked bodice provides a comfortable fit.</p>', 'price' => 89.99, 'weight' => 0.35, 'category_slug' => 'women-dresses', 'color_name' => 'Sage Green', 'size_name' => 'M', 'new' => true, 'featured' => false],
            ['name' => 'A-Line Shift Dress', 'sku' => 'SP-WMN-SHIFT-NVY', 'short_description' => 'A clean A-line shift dress in structured cotton — minimalist perfection.', 'description' => '<p>Simplicity speaks volumes with our A-Line Shift Dress. Cut from structured cotton poplin.</p>', 'price' => 69.99, 'weight' => 0.28, 'category_slug' => 'women-dresses', 'color_name' => 'Navy Blue', 'size_name' => 'S', 'new' => false, 'featured' => true],
            ['name' => 'Little Black Dress', 'sku' => 'SP-WMN-LBD-BLK', 'short_description' => 'The essential little black dress — sleek, timeless, endlessly versatile.', 'description' => '<p>Every wardrobe needs a Little Black Dress. Ours features a figure-skimming silhouette and elegant boat neckline.</p>', 'price' => 74.99, 'weight' => 0.22, 'category_slug' => 'women-dresses', 'color_name' => 'Midnight Black', 'size_name' => 'M', 'new' => true, 'featured' => true],

            // Women's Trousers & Jeans
            ['name' => 'High-Rise Skinny Jeans', 'sku' => 'SP-WMN-JEANS-BLK', 'short_description' => 'Figure-flattering high-rise skinny jeans in sculpting power-stretch denim.', 'description' => '<p>Our High-Rise Skinny Jeans are designed to flatter. Premium power-stretch denim sculpts and smooths.</p>', 'price' => 79.99, 'weight' => 0.45, 'category_slug' => 'women-trousers-jeans', 'color_name' => 'Midnight Black', 'size_name' => 'M', 'new' => true, 'featured' => true],
            ['name' => 'Wide-Leg Palazzo Trousers', 'sku' => 'SP-WMN-PALZ-GRY', 'short_description' => 'Flowing wide-leg palazzo trousers with a flattering high waist.', 'description' => '<p>Channel effortless elegance with our Wide-Leg Palazzo Trousers. A high, flat-front waistband gives way to sweeping wide legs.</p>', 'price' => 64.99, 'weight' => 0.3, 'category_slug' => 'women-trousers-jeans', 'color_name' => 'Charcoal Grey', 'size_name' => 'M', 'new' => false, 'featured' => false],
            ['name' => 'Tailored Culottes', 'sku' => 'SP-WMN-CULL-CML', 'short_description' => 'Modern tailored culottes in premium stretch fabric.', 'description' => '<p>Our Tailored Culottes combine the comfort of shorts with the elegance of trousers.</p>', 'price' => 54.99, 'weight' => 0.25, 'category_slug' => 'women-trousers-jeans', 'color_name' => 'Camel', 'size_name' => 'S', 'new' => true, 'featured' => false],

            // Women's Ethnic Wear
            ['name' => 'Embroidered Saree', 'sku' => 'SP-WMN-SAREE-BRG', 'short_description' => 'A gorgeous embroidered saree in rich georgette with matching blouse piece.', 'description' => '<p>Drape yourself in elegance with our Embroidered Saree. Crafted from lightweight georgette with intricate embroidery.</p>', 'price' => 129.99, 'weight' => 0.5, 'category_slug' => 'women-ethnic-wear', 'color_name' => 'Burgundy', 'size_name' => 'Free', 'new' => true, 'featured' => true],
            ['name' => 'Salwar Kameez Set', 'sku' => 'SP-WMN-SALWAR-YLW', 'short_description' => 'A classic salwar kameez in premium cotton with tonal embroidery.', 'description' => '<p>Our Salwar Kameez Set offers everyday elegance with clean lines and subtle tonal embroidery.</p>', 'price' => 74.99, 'weight' => 0.4, 'category_slug' => 'women-ethnic-wear', 'color_name' => 'Mustard Yellow', 'size_name' => 'M', 'new' => false, 'featured' => true],
            ['name' => 'Printed Lawn Suit', 'sku' => 'SP-WMN-LAWN-GRN', 'short_description' => 'A breezy printed lawn suit perfect for everyday wear.', 'description' => '<p>Our Printed Lawn Suit combines comfort with style. Soft, breathable lawn cotton with a vibrant print.</p>', 'price' => 49.99, 'weight' => 0.3, 'category_slug' => 'women-ethnic-wear', 'color_name' => 'Sage Green', 'size_name' => 'L', 'new' => true, 'featured' => false],

            // Kids' Boys
            ['name' => 'Graphic Print T-Shirt', 'sku' => 'SP-KID-BOY-GFX-NVY', 'short_description' => 'A fun graphic print t-shirt in soft cotton jersey for active boys.', 'description' => '<p>Let his personality shine with our Graphic Print T-Shirt. Made from soft, durable cotton jersey.</p>', 'price' => 19.99, 'weight' => 0.12, 'category_slug' => 'kids-boys', 'color_name' => 'Navy Blue', 'size_name' => '5-6', 'new' => true, 'featured' => true],
            ['name' => 'Denim Overalls', 'sku' => 'SP-KID-BOY-OVER-NVY', 'short_description' => 'Classic denim overalls with adjustable straps and snap-button sides.', 'description' => '<p>Our Denim Overalls are built for adventure. Sturdy yet soft denim with adjustable shoulder straps.</p>', 'price' => 34.99, 'weight' => 0.3, 'category_slug' => 'kids-boys', 'color_name' => 'Navy Blue', 'size_name' => '4-5', 'new' => false, 'featured' => false],
            ['name' => 'Striped Polo Shirt', 'sku' => 'SP-KID-BOY-POLO-GRN', 'short_description' => 'A smart striped polo shirt in soft cotton piqué.', 'description' => '<p>Our Striped Polo Shirt brings smart style to his everyday wardrobe.</p>', 'price' => 24.99, 'weight' => 0.15, 'category_slug' => 'kids-boys', 'color_name' => 'Sage Green', 'size_name' => '6-7', 'new' => true, 'featured' => false],

            // Kids' Girls
            ['name' => 'Floral Cotton Dress', 'sku' => 'SP-KID-GRL-FLRL-RSE', 'short_description' => 'A sweet floral cotton dress with twirly skirt and popper fastening.', 'description' => '<p>She will love the twirly skirt on our Floral Cotton Dress. Made from soft, breathable cotton.</p>', 'price' => 29.99, 'weight' => 0.15, 'category_slug' => 'kids-girls', 'color_name' => 'Dusty Rose', 'size_name' => '4-5', 'new' => true, 'featured' => true],
            ['name' => 'Leggings & Top Set', 'sku' => 'SP-KID-GRL-LEG-BRG', 'short_description' => 'A comfortable leggings and top set in soft stretch cotton.', 'description' => '<p>Our Leggings and Top Set is designed for comfort and fun.</p>', 'price' => 24.99, 'weight' => 0.15, 'category_slug' => 'kids-girls', 'color_name' => 'Burgundy', 'size_name' => '5-6', 'new' => false, 'featured' => true],
            ['name' => 'Sequinned Party Dress', 'sku' => 'SP-KID-GRL-PRTY-LVR', 'short_description' => 'A sparkly sequinned party dress for special occasions.', 'description' => '<p>She will be the star of the party in our Sequinned Party Dress.</p>', 'price' => 39.99, 'weight' => 0.2, 'category_slug' => 'kids-girls', 'color_name' => 'Lavender', 'size_name' => '3-4', 'new' => true, 'featured' => false],

            // Kids' Infants
            ['name' => 'Organic Cotton Romper', 'sku' => 'SP-KID-INF-ROMP-WHT', 'short_description' => 'A snuggly organic cotton romper with snap closures for easy changes.', 'description' => '<p>Wrap your little one in softness with our Organic Cotton Romper. Made from GOTS-certified organic cotton.</p>', 'price' => 22.99, 'weight' => 0.1, 'category_slug' => 'kids-infants', 'color_name' => 'Ivory White', 'size_name' => '1-2', 'new' => true, 'featured' => true],
            ['name' => 'Knit Bodysuit Pack', 'sku' => 'SP-KID-INF-BODY-CRM', 'short_description' => 'Essential knit bodysuits in soft cotton — everyday basics for baby.', 'description' => '<p>Our Knit Bodysuit Pack includes everyday essentials for your baby.</p>', 'price' => 18.99, 'weight' => 0.08, 'category_slug' => 'kids-infants', 'color_name' => 'Cream', 'size_name' => '1-2', 'new' => false, 'featured' => false],
            ['name' => 'Striped Sleepsuit', 'sku' => 'SP-KID-INF-SLEEP-SKY', 'short_description' => 'A cozy striped sleepsuit with built-in feet and front zipper.', 'description' => '<p>Our Striped Sleepsuit is the ultimate in baby comfort. Soft cotton with built-in feet.</p>', 'price' => 20.99, 'weight' => 0.1, 'category_slug' => 'kids-infants', 'color_name' => 'Sky Blue', 'size_name' => '2-3', 'new' => true, 'featured' => false],
        ];

        // Create configurable products
        foreach ($configurableProducts as $data) {
            $created += $this->createConfigurableProduct($data);
        }

        // Create simple products
        foreach ($simpleProducts as $data) {
            $created += $this->createSimpleProduct($data);
        }

        // Reindex all products to generate flat tables, prices, inventory
        $this->command->info("\n  Reindexing products...");
        try {
            $flatIndexer = app(FlatIndexer::class);
            $flatIndexer->reindexFull();
            $this->command->info('  ✓ Flat index refreshed');

            $priceIndexer = app(PriceIndexer::class);
            $priceIndexer->reindexFull();
            // Run twice: first pass creates variant price indices,
            // second pass computes configurable prices from variant indices
            $priceIndexer->reindexFull();
            $this->command->info('  ✓ Price index refreshed');

            $inventoryIndexer = app(InventoryIndexer::class);
            $inventoryIndexer->reindexFull();
            $this->command->info('  ✓ Inventory index refreshed');
        } catch (\Exception $e) {
            $this->command->error('  ✗ Reindexing failed: '.$e->getMessage());
        }

        $this->command->info("\n✨ Clothing products seeding complete!");
        $this->command->info("📊 Created: {$created} products");
    }

    // ═══════════════════════════════════════════════════════
    //  SETUP HELPERS
    // ═══════════════════════════════════════════════════════

    protected function createCustomColorOptions(): void
    {
        $colorAttribute = Attribute::where('code', 'color')->first();
        if (! $colorAttribute) {
            $this->command->error('Color attribute not found!');

            return;
        }

        foreach ($this->clothingColors as $name => $hex) {
            $existing = $colorAttribute->options()->where('admin_name', $name)->first();
            if ($existing) {
                $this->customColorIds[$name] = $existing->id;

                continue;
            }

            $option = AttributeOption::create([
                'attribute_id' => $colorAttribute->id,
                'admin_name' => $name,
                'swatch_value' => $hex,
                'sort_order' => ($colorAttribute->options()->max('sort_order') ?? 0) + 1,
            ]);

            foreach (core()->getAllLocales() as $locale) {
                DB::table('attribute_option_translations')->insert([
                    'attribute_option_id' => $option->id,
                    'locale' => $locale->code,
                    'label' => $name,
                ]);
            }

            $this->customColorIds[$name] = $option->id;
        }
    }

    protected function loadSizeOptions(): void
    {
        $sizeAttribute = Attribute::where('code', 'size')->first();
        if ($sizeAttribute) {
            foreach ($sizeAttribute->options as $option) {
                $this->sizeOptions[$option->admin_name] = $option->id;
            }
        }
    }

    protected function loadCategories(): void
    {
        foreach (CategoryTranslation::where('locale', 'en')->get() as $t) {
            $this->categoryMap[$t->slug] = $t->category_id;
        }
    }

    protected function loadFamilyAttributes(): void
    {
        $family = AttributeFamily::find(1);
        if ($family) {
            $this->familyAttributes = $family->custom_attributes;
        }
    }

    // ═══════════════════════════════════════════════════════
    //  PRODUCT CREATION
    // ═══════════════════════════════════════════════════════

    protected function createConfigurableProduct(array $data): int
    {
        $categoryId = $this->categoryMap[$data['category_slug']] ?? null;
        if (! $categoryId) {
            $this->command->warn("  ⚠ Category not found: {$data['category_slug']}");

            return 0;
        }

        try {
            // Step 1: Create the configurable product row (only fillable fields)
            $product = Product::create([
                'type' => 'configurable',
                'sku' => $data['sku'],
                'attribute_family_id' => 1,
            ]);

            // Step 2: Attach super attributes
            $colorAttribute = Attribute::where('code', 'color')->first();
            $sizeAttribute = Attribute::where('code', 'size')->first();

            if (! empty($data['color_options']) && $colorAttribute) {
                $product->super_attributes()->attach($colorAttribute->id);
            }

            if (! empty($data['size_options']) && $sizeAttribute) {
                $product->super_attributes()->attach($sizeAttribute->id);
            }

            // Step 3: Save parent attribute values (name, description, etc. — NOT price/weight for configurable)
            $parentData = [
                'locale' => core()->getDefaultLocaleCodeFromDefaultChannel(),
                'channel' => core()->getDefaultChannelCode(),
                'name' => $data['name'],
                'url_key' => $this->generateUrlKey($data['name']),
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'new' => $data['new'] ?? false,
                'featured' => $data['featured'] ?? false,
                'status' => 1,
                'visible_individually' => 1,
                'guest_checkout' => 1,
                'meta_title' => $data['name'],
                'meta_keywords' => $data['name'],
                'meta_description' => $data['short_description'],
            ];

            $this->attributeValueRepository->saveValues($parentData, $product, $this->familyAttributes);

            // Step 4: Sync channel and category
            $product->channels()->sync(core()->getDefaultChannel()->id);
            $product->categories()->sync([$categoryId]);

            // Step 5: Create variants
            $superAttrMap = [];
            if (! empty($data['color_options'])) {
                $ids = [];
                foreach ($data['color_options'] as $c) {
                    if (isset($this->customColorIds[$c])) {
                        $ids[] = $this->customColorIds[$c];
                    }
                }
                if ($ids) {
                    $superAttrMap['color'] = $ids;
                }
            }
            if (! empty($data['size_options'])) {
                $ids = [];
                foreach ($data['size_options'] as $s) {
                    if (isset($this->sizeOptions[$s])) {
                        $ids[] = $this->sizeOptions[$s];
                    }
                }
                if ($ids) {
                    $superAttrMap['size'] = $ids;
                }
            }

            // Load fillable variant attributes
            $fillableVariantAttributes = AttributeRepository::class
                ? app(AttributeRepository::class)
                    ->findWhereIn('code', ['sku', 'name', 'url_key', 'short_description', 'description', 'price', 'weight', 'status', 'tax_category_id'])
                : collect();

            // Add super attributes to fillable list
            if ($colorAttribute) {
                $fillableVariantAttributes->push($colorAttribute);
            }
            if ($sizeAttribute) {
                $fillableVariantAttributes->push($sizeAttribute);
            }

            $permutations = $this->arrayPermutation($superAttrMap);
            $variantCount = 0;

            foreach ($permutations as $permutation) {
                $suffixParts = [];
                foreach ($permutation as $attrCode => $optionId) {
                    $opt = AttributeOption::find($optionId);
                    if ($opt) {
                        $suffixParts[] = $opt->admin_name;
                    }
                }

                $variantSku = $data['sku'].'-variant-'.implode('-', $permutation);

                // Create variant product row
                $variant = Product::create([
                    'type' => 'simple',
                    'sku' => $variantSku,
                    'attribute_family_id' => 1,
                    'parent_id' => $product->id,
                ]);

                // Save variant attribute values
                $variantData = [
                    'locale' => core()->getDefaultLocaleCodeFromDefaultChannel(),
                    'channel' => core()->getDefaultChannelCode(),
                    'sku' => $variantSku,
                    'name' => $data['name'].' - '.implode(' / ', $suffixParts),
                    'url_key' => $variantSku,
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'weight' => $data['weight'],
                    'status' => 1,
                    'color' => $permutation['color'] ?? null,
                    'size' => $permutation['size'] ?? null,
                ];

                $this->attributeValueRepository->saveValues($variantData, $variant, $fillableVariantAttributes);

                // Save variant inventory
                $this->inventoryRepository->saveInventories([
                    'inventories' => [1 => rand(20, 100)],
                ], $variant);

                // Sync variant channels
                $variant->channels()->sync(core()->getDefaultChannel()->id);

                $variantCount++;
            }

            $this->command->info("  ✓ Configurable: {$data['name']} ({$variantCount} variants)");

            return 1;
        } catch (\Exception $e) {
            $this->command->error("  ✗ Failed: {$data['name']} — ".$e->getMessage());

            return 0;
        }
    }

    protected function createSimpleProduct(array $data): int
    {
        $categoryId = $this->categoryMap[$data['category_slug']] ?? null;
        if (! $categoryId) {
            $this->command->warn("  ⚠ Category not found: {$data['category_slug']}");

            return 0;
        }

        try {
            // Step 1: Create the product row (only fillable fields)
            $product = Product::create([
                'type' => 'simple',
                'sku' => $data['sku'],
                'attribute_family_id' => 1,
            ]);

            // Step 2: Save attribute values via EAV
            $attrData = [
                'locale' => core()->getDefaultLocaleCodeFromDefaultChannel(),
                'channel' => core()->getDefaultChannelCode(),
                'name' => $data['name'],
                'url_key' => $this->generateUrlKey($data['name']),
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'price' => $data['price'],
                'weight' => $data['weight'],
                'new' => $data['new'] ?? false,
                'featured' => $data['featured'] ?? false,
                'status' => 1,
                'visible_individually' => 1,
                'guest_checkout' => 1,
                'meta_title' => $data['name'],
                'meta_keywords' => $data['name'],
                'meta_description' => $data['short_description'],
            ];

            if (! empty($data['color_name']) && isset($this->customColorIds[$data['color_name']])) {
                $attrData['color'] = $this->customColorIds[$data['color_name']];
            }

            if (! empty($data['size_name']) && isset($this->sizeOptions[$data['size_name']])) {
                $attrData['size'] = $this->sizeOptions[$data['size_name']];
            }

            $this->attributeValueRepository->saveValues($attrData, $product, $this->familyAttributes);

            // Step 3: Sync channel and category
            $product->channels()->sync(core()->getDefaultChannel()->id);
            $product->categories()->sync([$categoryId]);

            // Step 4: Save inventory
            $this->inventoryRepository->saveInventories([
                'inventories' => [1 => rand(20, 100)],
            ], $product);

            $this->command->info("  ✓ Simple: {$data['name']}");

            return 1;
        } catch (\Exception $e) {
            $this->command->error("  ✗ Failed: {$data['name']} — ".$e->getMessage());

            return 0;
        }
    }

    // ═══════════════════════════════════════════════════════
    //  UTILITIES
    // ═══════════════════════════════════════════════════════

    protected function arrayPermutation(array $input): array
    {
        $result = [[]];
        foreach ($input as $key => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $value) {
                    $product[$key] = $value;
                    $append[] = $product;
                }
            }
            $result = $append;
        }

        return $result;
    }

    protected function generateUrlKey(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug .= '-'.$this->urlKeyCounter++;

        return $slug;
    }
}
