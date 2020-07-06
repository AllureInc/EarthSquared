<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dolphin\Wishlist\CustomerData;

use Magento\Framework\App\ObjectManager;

/**
 * Wishlist section
 */
class Wishlist extends \Magento\Wishlist\CustomerData\Wishlist
{
    /**
     * @var string
     */
    const SIDEBAR_ITEMS_NUMBER = 3;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $wishlistHelper;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $imageHelperFactory;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $view;

    /**
     * @var \Magento\Wishlist\Block\Customer\Sidebar
     */
    protected $block;

    /**
     * @var \Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface
     */
    private $itemResolver;

    private $Options;

    private $priceHelper;

    /**
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Wishlist\Block\Customer\Sidebar $block
     * @param \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
     * @param \Magento\Framework\App\ViewInterface $view
     * @param \Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface|null $itemResolver
     */
    public function __construct(
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Wishlist\Block\Customer\Sidebar $block,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface $itemResolver = null,
        \Magento\Wishlist\Block\Customer\Wishlist\Item\Options $Options,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->block = $block;
        $this->view = $view;
        $this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(
            \Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface::class
        );
        $this->Options = $Options;
        $this->priceHelper = $priceHelper;

    }

    /**
     * @return string
     */
    protected function getCounterHeader()
    {
        return $this->createCounterHeader($this->wishlistHelper->getItemCount());
    }

    /**
     * Create button label based on wishlist item quantity
     *
     * @param int $count
     * @return \Magento\Framework\Phrase|null
     */
    protected function createCounterHeader($count)
    {
        if ($count > 1) {
            return __('%1', $count);
        } elseif ($count == 1) {
            return __('1');
        }
        return null;
    }

    public function getSectionData()
    {
        $counter = $this->getCounter();
        $counter_header = $this->getCounterHeader();
        return [
            'counter_header' => $counter_header,
            'counter' => $counter,
            'subtotal' => $this->priceHelper->currency($this->getSubTotal(), true, false),
            'items' => $counter ? $this->getItems() : [],

        ];
    }
    protected function getSubTotal()
    {
        $collection = $this->wishlistHelper->getWishlistItemCollection();
        //$collection->clear()->setPageSize(self::SIDEBAR_ITEMS_NUMBER)->setInStockFilter(true)->setOrder('added_at');

        $price = [];
        foreach ($collection as $wishlistItem) {
            $product = $wishlistItem->getProduct();
            $price[] = $product->getFinalPrice();
        }
        return array_sum($price);
    }
    protected function getItemData(\Magento\Wishlist\Model\Item $wishlistItem)
    {
        $product = $wishlistItem->getProduct();
        $attributs = $wishlistItem->getBuyRequest()->getSuperAttribute();
        $optionText = false;
        if (@$attributs[92]) {
            $isAttributeExist = $product->getResource()->getAttribute('color');
            if ($isAttributeExist && $isAttributeExist->usesSource()) {
                //echo $attributs[92];
                $optionText = $isAttributeExist->getSource()->getOptionText($attributs[92]);
            }
        }
        return [
            'image' => $this->getImageData($this->itemResolver->getFinalProduct($wishlistItem)),
            'product_sku' => $product->getSku(),
            'product_id' => $product->getId(),
            'product_url' => $this->wishlistHelper->getProductUrl($wishlistItem),
            'product_name' => $product->getName(),
            'product_price' => $this->block->getProductPriceHtml(
                $product,
                'wishlist_configured_price',
                \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                ['item' => $wishlistItem]
            ),
            'product_is_saleable_and_visible' => $product->isSaleable() && $product->isVisibleInSiteVisibility(),
            'product_has_required_options' => $product->getTypeInstance()->hasRequiredOptions($product),
            'add_to_cart_params' => $this->wishlistHelper->getAddToCartParams($wishlistItem),
            'delete_item_params' => $this->wishlistHelper->getRemoveParams($wishlistItem),
            'attribute' => $optionText,
        ];
    }

}
