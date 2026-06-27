<?php

namespace Frooxi\Product\Helpers\Indexers\Price;

use Frooxi\Core\Contracts\Channel;
// REMOVED: CatalogRule package deleted
// use Frooxi\CatalogRule\Repositories\CatalogRuleProductPriceRepository;
use Frooxi\Customer\Contracts\CustomerGroup;
use Frooxi\Customer\Repositories\CustomerRepository;
use Frooxi\Product\Contracts\Product;
use Frooxi\Product\Repositories\ProductCustomerGroupPriceRepository;
use Illuminate\Support\Carbon;

abstract class AbstractType
{
    /**
     * Product instance.
     *
     * @var Product
     */
    protected $product;

    /**
     * Channel instance.
     *
     * @var Channel
     */
    protected $channel;

    /**
     * Customer Group instance.
     *
     * @var CustomerGroup
     */
    protected $customerGroup;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected ProductCustomerGroupPriceRepository $productCustomerGroupPriceRepository
        // REMOVED: CatalogRule package deleted
        // protected CatalogRuleProductPriceRepository $catalogRuleProductPriceRepository
    ) {}

    /**
     * Set current product
     *
     * @param  Product  $product
     * @return AbstractPriceIndex
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Set channel
     *
     * @param  Channel  $channel
     * @return AbstractPriceIndex
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Set customer group
     *
     * @param  CustomerGroup  $customerGroup
     * @return AbstractPriceIndex
     */
    public function setCustomerGroup($customerGroup)
    {
        $this->customerGroup = $customerGroup;

        return $this;
    }

    /**
     * Returns product specific pricing for customer group
     *
     * @return array
     */
    public function getIndices()
    {
        return [
            'min_price' => ($minPrice = $this->getMinimalPrice()) ?? 0,
            'regular_min_price' => $this->product->price ?? 0,
            'max_price' => $minPrice ?? 0,
            'regular_max_price' => $this->product->price ?? 0,
            'product_id' => $this->product->id,
            'channel_id' => $this->channel->id,
            'customer_group_id' => $this->customerGroup->id,
        ];
    }

    /**
     * Get product minimal price.
     *
     * @param  int  $qty
     * @return float
     */
    public function getMinimalPrice($qty = null)
    {
        $customerGroupPrice = $this->getCustomerGroupPrice($qty ?? 1);

        // REMOVED: CatalogRule package deleted
        // $rulePrice = $this->getCatalogRulePrice();
        $rulePrice = null;

        $discountPercentage = (float) $this->product->discount_percentage;
        $hasDiscountPercentage = $discountPercentage > 0 && $discountPercentage <= 100;
        
        $specialPrice = $this->product->special_price;
        if ($hasDiscountPercentage) {
            $specialPrice = $this->product->price - ($this->product->price * $discountPercentage / 100);
        }

        if (
            empty($specialPrice)
            && empty($rulePrice)
            && $customerGroupPrice == $this->product->price
        ) {
            return $this->product->price;
        }

        if (! (float) $specialPrice) {
            if ($rulePrice) {
                $discountedPrice = min($rulePrice->price, $this->product->price);
            } else {
                $discountedPrice = $this->product->price;
            }
        } else {
            if ($rulePrice) {
                if (
                    $hasDiscountPercentage ||
                    core()->isChannelDateInInterval(
                        $this->product->special_price_from,
                        $this->product->special_price_to
                    )
                ) {
                    $discountedPrice = min($rulePrice->price, $specialPrice);
                } else {
                    $discountedPrice = $rulePrice->price;
                }
            } else {
                if (
                    $hasDiscountPercentage ||
                    core()->isChannelDateInInterval(
                        $this->product->special_price_from,
                        $this->product->special_price_to
                    )
                ) {
                    $discountedPrice = $specialPrice;
                } else {
                    $discountedPrice = $this->product->price;
                }
            }
        }

        return min($discountedPrice, $customerGroupPrice);
    }

    /**
     * Get product group price.
     *
     * @param  int  $qty
     * @return float
     */
    public function getCustomerGroupPrice($qty)
    {
        $customerGroupPrices = $this->productCustomerGroupPriceRepository
            ->prices($this->product, $this->customerGroup->id);

        if ($customerGroupPrices->isEmpty()) {
            return $this->product->price;
        }

        $lastQty = 1;

        $lastPrice = $this->product->price;

        foreach ($customerGroupPrices as $customerGroupPrice) {
            if (
                $customerGroupPrice->qty > $qty
                || $customerGroupPrice->qty < $lastQty
            ) {
                continue;
            }

            if ($customerGroupPrice->value_type == 'discount') {
                if (
                    $customerGroupPrice->value >= 0
                    && $customerGroupPrice->value <= 100
                ) {
                    $lastPrice = $this->product->price - ($this->product->price * $customerGroupPrice->value) / 100;

                    $lastQty = $customerGroupPrice->qty;
                }
            } else {
                if (
                    $customerGroupPrice->value >= 0
                    && $customerGroupPrice->value < $lastPrice
                ) {
                    $lastPrice = $customerGroupPrice->value;

                    $lastQty = $customerGroupPrice->qty;
                }
            }
        }

        return $lastPrice;
    }

    /**
     * Get catalog rules product price for specific date, channel and customer group.
     *
     * @return mixed
     */
    // REMOVED: CatalogRule package deleted
    /*
    public function getCatalogRulePrice()
    {
        return $this->product->catalog_rule_prices
            ->where('customer_group_id', $this->customerGroup->id)
            ->where('channel_id', $this->channel->id)
            ->where('rule_date', Carbon::now()->format('Y-m-d'))
            ->first();
    }
    */
}
