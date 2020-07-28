<?php
namespace Dolphin\QuickOrder\Block;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;

class Index extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    protected $_categoryFactory;
    protected $priceHelper;
    protected $_categoryCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
    	$this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->priceHelper = $priceHelper;
        $this->listProductBlock = $listProductBlock;
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
    public function getProductCollectionByCategory($id, $subcatname)
    {
    	$storeid = 1;        
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;        
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $id]);          
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addStoreFilter($storeid);   
        

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
        // print_r($collection->getData('category_ids'));
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
}