<?php
namespace Earthsquared\Customize\Block;

use Magento\Catalog\Block\Product\ListProduct;

class FeatureProducts extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    protected $_categoryFactory;
    protected $priceHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->priceHelper = $priceHelper;
        $this->listProductBlock = $listProductBlock;
        parent::__construct($context);
    }

    public function getProductCollectionByCategory($id)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $id]);
        $collection->setPageSize(4);
        return $collection;
    }

    public function getProductPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }
}
