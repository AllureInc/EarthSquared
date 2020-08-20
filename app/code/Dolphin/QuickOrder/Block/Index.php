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
    public function getProductCollectionByCategory()
    {        
        $storeid = 1;
        $parentId1 = 238;
        $parentId2 = 3; 
        $awchildids = array();
        $sschildids = array();        

        // group 1 collection 
        $awcollection = $this->getChildCategoriesCollection($parentId1);             
        foreach($awcollection as $subcategory)
        {       
            $awchildids[] = $subcategory->getId();
        }
 
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 1;
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $awchildids]);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('type_id', ['eq' => 'simple']);     
        $collection->addAttributeToFilter('visibility', 1);                      
        $collection->addStoreFilter($storeid);
        
        $collection->getSelect()->join(
            'catalog_category_product',
            'e.entity_id=`catalog_category_product`.product_id', ['category_id', 'product_id'])->join(
            'catalog_category_entity_varchar',
            new \Zend_Db_Expr('`catalog_category_entity_varchar`.entity_id=`catalog_category_product`.category_id AND catalog_category_entity_varchar.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'name\' and entity_type_id = 3)'),
            array(
                'categories' => new \Zend_Db_Expr('group_concat(`catalog_category_entity_varchar`.value SEPARATOR ",")'))
        )->where('catalog_category_product.category_id IN(' . implode(',', $awchildids) . ')')->group('e.entity_id')->order('catalog_category_product.category_id ASC');

        // group 2 collection     
        $sscollection = $this->getChildCategoriesCollection($parentId2);             
        foreach($sscollection as $subcategory)
        {       
            $sschildids[] = $subcategory->getId();
        }

        $collection1 = $this->_productCollectionFactory->create();
        $collection1->addAttributeToSelect('*');
        $collection1->addCategoriesFilter(['in' => $sschildids]);
        $collection1->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection1->addAttributeToFilter('type_id', ['eq' => 'simple']);     
        $collection1->addAttributeToFilter('visibility', 1);                      
        $collection1->addStoreFilter($storeid);
        
        $collection1->getSelect()->join(
            'catalog_category_product',
            'e.entity_id=`catalog_category_product`.product_id', ['category_id', 'product_id'])->join(
            'catalog_category_entity_varchar',
            new \Zend_Db_Expr('`catalog_category_entity_varchar`.entity_id=`catalog_category_product`.category_id AND catalog_category_entity_varchar.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'name\' and entity_type_id = 3)'),
            array(
                'categories' => new \Zend_Db_Expr('group_concat(`catalog_category_entity_varchar`.value SEPARATOR ",")'))
        )->where('catalog_category_product.category_id IN(' . implode(',', $sschildids) . ')')->group('e.entity_id')->order('catalog_category_product.category_id ASC');

        // echo $collection->getSelect();
        // echo "<br/>";
        // echo $collection1->getSelect();
        // exit;

        // Now set both collection group array                 
        $awgroup = array(); 
        foreach($collection as $group1)
        {            
            $awgroup[] = $group1->getId();
            $awCatids[] = $group1->getData('category_id');
        } 
        $ssgroup = array();    
        
        foreach($collection1 as $group2)
        {            
            $ssgroup[] = $group2->getId();
            $ssCatids[] = $group2->getData('category_id');
        } 

        // here we merge both collection group
        $merged_ids = array_merge($awgroup, $ssgroup);
        $merged_cat_ids = array_merge($awCatids, $ssCatids);
        
        

        $mergeCollection = $this->_productCollectionFactory->create();
        $mergeCollection->addAttributeToSelect('*');        
        $mergeCollection->addAttributeToFilter('entity_id', ['in' => $merged_ids]);            
        $mergeCollection->joinField('category_id','catalog_category_product','category_id','product_id = entity_id', null, 'left');   
        $mergeCollection->addAttributeToFilter('category_id', array('in' => array_unique($merged_cat_ids)));
        $mergeCollection->getSelect()->order("find_in_set(e.entity_id,'" . implode(',', $merged_ids) . "')")->group('e.entity_id');
        
        // $mergeCollection->getSelect()->join(
        //     'catalog_category_product',
        //     'e.entity_id=`catalog_category_product`.product_id', ['category_id', 'product_id'])->join(
        //     'catalog_category_entity_varchar',
        //     new \Zend_Db_Expr('`catalog_category_entity_varchar`.entity_id=`catalog_category_product`.category_id AND catalog_category_entity_varchar.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'name\' and entity_type_id = 3)'),
        //     array(
        //         'categories' => new \Zend_Db_Expr('group_concat(`catalog_category_entity_varchar`.value SEPARATOR ",")'))
        // )->where('catalog_category_product.category_id IN(' .  implode(',',array_unique($merged_cat_ids) . ')')->group('e.entity_id');

         // echo $mergeCollection->getSelect();
         //exit;
         // echo "<pre>";
         // print_r($mergeCollection->getData());
         // exit;

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'test.news.pager'
        )->setShowPerPage(true)->setCollection(
            $mergeCollection
        );
        $this->setChild('pager', $pager);
        $mergeCollection->setPageSize(100);
        $mergeCollection->setCurPage($page);
        $mergeCollection->load();

        // echo "<pre>";
        // print_r($collection->getData());
        // exit;
        return $mergeCollection;
    } 
    public function getCountProductCollectionByCategory($id)
    {        
        $storeid = 1;                
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addCategoriesFilter(['in' => $id]);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('type_id', ['eq' => 'simple']);     
        $collection->addAttributeToFilter('visibility', 1);              
        $collection->addStoreFilter($storeid);
                
        $collection->joinField('category_id','catalog_category_product','category_id','product_id = entity_id', null, 'left');   
        $collection->addAttributeToFilter('category_id', array('in' => $id));        
        return count($collection->getData());        
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
