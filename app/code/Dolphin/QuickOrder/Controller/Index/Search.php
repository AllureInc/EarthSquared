<?php
namespace Dolphin\QuickOrder\Controller\Index;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;

class Search extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $coreSession;
    protected $resultJsonFactory;
    protected $_productCollectionFactory;
    protected $_categoryFactory;
    protected $priceHelper;
    protected $_categoryCollectionFactory;
    protected $imageHelper;    
    protected $layoutFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,        
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->coreSession = $coreSession;
        $this->_objectManager = $objectManager;
        $this->cart = $cart;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->priceHelper = $priceHelper;
        $this->listProductBlock = $listProductBlock;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->imageHelper = $imageHelper;
        $this->layoutFactory = $layoutFactory;
        return parent::__construct($context);
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
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $categoryIds = [238,3];
        $querySearch = $_REQUEST['querysearch'];

        $ids = array();
        $subname = array();
        $productCount = array();
        $sproducts = array();
        $response = array();

        $categories = $this->getCategoryCollection()->addAttributeToFilter('entity_id', $categoryIds);
        foreach ($categories as $category) {
            $maincategoryId = $category->getId();
            $collection = $this->getChildCategoriesCollection($maincategoryId);
            foreach($collection as $subcategory)
            {
                    $ids[] = $subcategory->getId();
            }
        }
        $scategoryProducts = $this->getsProductCollectionByCategory($ids,$querySearch);
        $displayCategoryId = '';         
        foreach($scategoryProducts as $product)
        {
            if($product->getLifestyleImage() != ""){
                $image_url = $this->imageHelper->init($product, 'product_small_image_hover')->constrainOnly(true)->keepAspectRatio(true)->keepTransparency(true)->keepFrame(true)->setImageFile($product->getFile())->resize(140, 140)->getUrl();
            } else {
                $image_url = $this->imageHelper->init($product, 'product_small_image')->constrainOnly(true)->keepAspectRatio(true)->keepTransparency(true)->keepFrame(true)->setImageFile($product->getFile())->resize(140, 140)->getUrl();
            }
            $type = 'widget-new-grid';

            $displayCategoryId = $product->getData('category_id');
            if(isset($subcatname[$displayCategoryId])){
                echo $subcatname[$displayCategoryId];
                echo '<span>('.$productcount[$displayCategoryId].' items)</span>';
            }
            
            $response[] =  ['name' => $product->getName(), 'sku' => $product->getSku(), 'id' => $product->getId(),'price' => $this->priceHelper->currency($product->getPrice(), true, false), 'image' => $image_url, 'category_id' => $product->getData('category_id'), 'product_url' => $product->getProductUrl()];           
        }                
        return $resultJson->setData($response);
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
    public function getsProductCollectionByCategory($id,$querySearch)
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
        $collection->getSelect()->join(
            'catalog_category_product',
            'e.entity_id=`catalog_category_product`.product_id', ['category_id', 'product_id'])->join(
            'catalog_category_entity_varchar',
            new \Zend_Db_Expr('`catalog_category_entity_varchar`.entity_id=`catalog_category_product`.category_id AND catalog_category_entity_varchar.attribute_id = (select attribute_id from eav_attribute where attribute_code = \'name\' and entity_type_id = 3)'),
            array(
                'categories' => new \Zend_Db_Expr('group_concat(`catalog_category_entity_varchar`.value SEPARATOR ",")'))
        )->where('catalog_category_product.category_id IN(' . implode(',', $id) . ')')->group('e.entity_id');//->order('catalog_category_product.category_id ASC');        
        $collection->addAttributeToFilter(
        [
            ['attribute' => 'name', 'like' => '%'.$querySearch.'%'],
            ['attribute' => 'sku', 'like' => '%'.$querySearch.'%']
        ]);
        //echo $collection->getSelect();exit;
        // echo "<pre>";
        // print_r($collection->getData());
        // exit;        
        // $pager = $this->layoutFactory->create()->createBlock(
        //      'Magento\Theme\Block\Html\Pager',
        //      'Magento_Theme::html/pager.phtml'
        // )->setShowPerPage(true)->setCollection(
        //      $collection
        // );        
        
        // $collection->setPageSize(100);
        // $collection->setCurPage($page);
        $collection->load(); 
        // echo "<pre>";
        // print_r($collection->getData());                       
        // exit;
        return $collection;
    }        
}
