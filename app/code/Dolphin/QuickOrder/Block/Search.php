<?php
namespace Dolphin\QuickOrder\Block;
 
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;

class Search extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    protected $_categoryFactory;
    protected $priceHelper;
    protected $_categoryCollectionFactory;
    protected $categoryHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Helper\Category $categoryHelper
    ) {
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->priceHelper = $priceHelper;
        $this->listProductBlock = $listProductBlock;
        $this->_category = $categoryHelper;
        parent::__construct($context);
    }
    protected function getDetailsRendererList()
    {
        return $this->getDetailsRendererListName() ? $this->getLayout()->getBlock(
            $this->getDetailsRendererListName()
        ) : $this->getChildBlock(
            'quickorder.toprenderers'
        );
    }
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }

    public function getDetailsRenderer($type = null)
    {
        if ($type === null) {
            $type = 'default';
        }
        $rendererList = $this->getDetailsRendererList();
        if ($rendererList) {
            return $rendererList->getRenderer($type, 'default');
        }
        return null;
    }

    public function getProductPricetoHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null
    ) {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product
            );
        }
        return $price;
    }
    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
    }

    public function getChildCategoriesCollection($id)
    {
        $category = $this->_categoryFactory->create()->load($id);
        $childrenCategories = $category->getChildrenCategories();
        return $childrenCategories;
    }
    public function getProductCollectionByCategory($id)
    {
        $storeid = 1;
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 1;
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $id]);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('type_id', 'simple');
        $collection->addStoreFilter($storeid);
        //echo $collection->getSelect();exit;
        $collection->getSelect()->join(
            'catalog_category_product',
            'e.entity_id=`catalog_category_product`.product_id', ['category_id', 'product_id'])->join(
            'catalog_category_entity_varchar',
            new \Zend_Db_Expr('`catalog_category_entity_varchar`.entity_id=`catalog_category_product`.category_id AND catalog_category_entity_varchar.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'name\' and entity_type_id = 3)'),
            array(
                'categories' => new \Zend_Db_Expr('group_concat(`catalog_category_entity_varchar`.value SEPARATOR ",")'))
        )->where('catalog_category_product.category_id IN(' . implode(',', $id) . ')')->group('e.entity_id')->order('catalog_category_product.category_id ASC');
        // echo "<pre>";
        // print_r($collection->getData());
        // exit;

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'test.news.pager'
        )->setShowPerPage(true)->setCollection(
            $collection
        );
        $this->setChild('pager', $pager);
        $collection->setPageSize(20);
        $collection->setCurPage($page);
        $collection->load();

        // echo "<pre>";
        // print_r($collection->getData());
        // exit;
        return $collection;
    }    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    public function getProductPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }
    public function getAddToWishlistParams($product)
    {
        return $this->_wishlistHelper->getAddParams($product);
    }
    public function getAddToCompareUrl()
    {
        return $this->_compareProduct->getAddUrl();
    }
    public function getParentCategory($categoryId = false)
    {
        return $this->getCategory($categoryId)->getParentCategory();                
    }    
    public function getCategory($categoryId) 
    {
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);        
        return $this->_category;
    }    
}