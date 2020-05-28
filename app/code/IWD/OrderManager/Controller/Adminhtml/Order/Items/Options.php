<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Items;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\DataObject;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Sales\Controller\Adminhtml\Order\Create;
use IWD\OrderManager\Model\Quote\Item;
use IWD\OrderManager\Model\Order\Converter;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class Options
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Items
 */
class Options extends Create
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_items_edit';

    /**
     * @var \IWD\OrderManager\Model\Order\Item $item
     */
    private $product;

    /**
     * @var \IWD\OrderManager\Model\Order\Converter $orderConverter
     */
    private $orderConverter;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    private $stockRegistry;

    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;


    protected $_mediaDirectory;


    protected $_fileUploaderFactory;


    protected $orderItemRepository;


    private $productFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \IWD\OrderManager\Model\Order\Converter $orderConverter
     */
    public function __construct(
        Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \IWD\OrderManager\Model\Order\Converter $orderConverter,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
        ProductFactory $productFactory
    ) {
        parent::__construct($context, $productHelper, $escaper, $resultPageFactory, $resultForwardFactory);

        $this->stockRegistry = $stockRegistry;
        $this->orderConverter = $orderConverter;
        $this->session = $context->getSession();
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->orderItemRepository = $orderItemRepository;
        $this->productFactory = $productFactory;
    }

    private function _upload($key,$name){
        try{
            $filePath = 'custom_options/quote/'.substr($name,0, 1).'/'.substr($name,1, 1).'/';
            $target = $this->_mediaDirectory->getAbsolutePath($filePath);
            $uploader = $this->_fileUploaderFactory->create(['fileId' => $key]);
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($target);
            if ($result['file']) {
                $path = explode('/pub/media/',$result['path'].$result['file']);
                return array(
                    'type' => $result['type'],
                    'title' => $result['file'],
                    'fullpath' => $result['path'].$result['file'],
                    'quote_path' => $path[1],
                    'order_path' => $path[1],
                    'size' => $result['size'],
                    'width' => 0,
                    'height' => 0,
                    'secret_key' => substr(hash_file('md5', $result['path'].$result['file']), 0, 20),
                );
            }
        } catch (\Exception $e) {
            return array();
        }
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $updateResult = new DataObject();
        $product = $this->productFactory->create();
        /** @var \IWD\OrderManager\Model\Order\Item $orderItem */

        try {
            $orderItemId = $this->getRequest()->getParam('id');
            $params = $this->getRequest()->getParams();
            $prefixIdLength = strlen(Item::PREFIX_ID);
            $files = $this->getRequest()->getFiles();

            if(substr($orderItemId, 0, $prefixIdLength) != Item::PREFIX_ID){
                $orderItem = $this->orderItemRepository->get($orderItemId);
                $quoteItemId = $orderItem->getQuoteItemId();
            }

            if(gettype($files) == 'object' && count($files) > 0){
                foreach($files as $key => $file){
                    if($file['size'] == 0){continue;}
                    $result = $this->_upload($key,strtolower($file['name']));
                    if(empty($result)){continue;}
                    $keyArray = explode('_',$key);
                    $keyOption = $keyArray['1'];
                    $params['options'][$keyOption] = $result;
                }
            }

            if (substr($orderItemId, 0, $prefixIdLength) == Item::PREFIX_ID || isset($quoteItemId)) {
                if(!isset($quoteItemId)){
                    $quoteItemId = substr($orderItemId, $prefixIdLength, strlen($orderItemId));
                }
                $orderItem = $this->orderConverter
                    ->convertQuoteItemToOrderItem($quoteItemId, $params);
            } else {

                $orderItem = $this->orderConverter
                    ->createNewOrderItem($orderItemId, $params);

                $this->orderConverter->convertQuoteItemToOrderItem($orderItem->getQuoteItemId(),$params);

                $orderItem->setId($orderItemId);
            }

            $sku = $product->load($orderItem->getProductId())->getSku();
            if($sku != $orderItem->getSku()){
                $orderItem->setSku($sku);
            }

            $resultPage = $this->resultPageFactory->create();
            /** @var \IWD\OrderManager\Block\Adminhtml\Order\Items\Options $optionsBlock */
            $optionsBlock = $resultPage->getLayout()
                ->getBlock('iwdordermamager_order_item_options');
            if (!empty($optionsBlock)) {
                $optionsHtml = $optionsBlock
                    ->setOrderItem($orderItem)
                    ->toHtml();

                $updateResult->setOptionsHtml($optionsHtml);
            }

            $productOptions = $orderItem->getData('product_options');
            $options = $this->prepareItemOptions($productOptions);

            $updateResult->setProductOptions($options);

            $updateResult->setPrice($orderItem->getData('base_price'));
            $updateResult->setName($orderItem->getData('name'));
            $updateResult->setSku($orderItem->getData('sku'));
            $updateResult->setItemId($orderItemId);

            $stock = $this->getStockObjectForOrderItem($orderItem);
            $updateResult->setStock($stock);

            $updateResult->setOk(true);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $updateResult->setError(true);
            $updateResult->setMessage($errorMessage);
        }

        $jsVarName = $this->getRequest()->getParam('as_js_varname');
        $updateResult->setJsVarName($jsVarName);

        $this->session->setCompositeProductResult($updateResult);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('catalog/product/showUpdateResult');
    }

    /**
     * @param $productOptionsData
     * @return string
     */
    private function prepareItemOptions($productOptionsData)
    {
        array_walk_recursive($productOptionsData, function (&$item) {
            if (is_string($item)) {
                $string = strip_tags($item);
                $string = preg_replace('/([^\pL\pN\pP\pS\pZ])|([\xC2\xA0])/u', ' ', $string);
                $string = str_replace(' ',' ', $string);
                $string = trim($string);
                $item = $string;
            }
        });

        if (strpos(Converter::getMagentoVersion(), '2.1.') !== 0){
            return json_encode($productOptionsData);
        }else{
            return serialize($productOptionsData);
        }
    }

    /**
     * @param \IWD\OrderManager\Model\Order\Item $orderItem
     * @return float[]
     */
    public function getStockObjectForOrderItem($orderItem)
    {
        if ($orderItem->getProductType() == 'configurable') {
            $simpleSku = $orderItem->getSku();
            $options = $orderItem->getData('product_options');
            if (isset($options['simple_sku'])) {
                $simpleSku = $options['simple_sku'];
            }
            $stock = $this->stockRegistry->getStockItemBySku(
                $simpleSku,
                $orderItem->getStore()->getWebsiteId()
            );
        } else {
            $simpleId = $orderItem->getProductId();
            $stock = $this->stockRegistry->getStockItem(
                $simpleId,
                $orderItem->getStore()->getWebsiteId()
            );
        }

        $stockQtyIncrements = $stock->getQtyIncrements();
        $stockQty = $stock->getQty();

        return [
            'data-stock-validate' => $this->isStockValidation($orderItem, $stockQty),
            'data-stock-qty-increment' => $stockQtyIncrements ? $stockQtyIncrements : 1,
            'data-stock-qty' => $stockQty ? $stockQty : 1,
            'data-stock-qty-min' => $stock->getMinQty() ? $stock->getMinQty() : 1,
            'data-stock-min-sales-qty' => $stock->getMinSaleQty() ? $stock->getMinSaleQty() : 1,
            'data-stock-max-sales-qty' => $stock->getMaxSaleQty() ? $stock->getMaxSaleQty() : 1,
        ];
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @param int $stockQty
     * @return bool
     */
    public function isStockValidation($item, $stockQty)
    {
        $productType = $item->getProductType();
        $isVirtual = in_array($productType, ['downloadable', 'virtual']);

        return $isVirtual && empty($stockQty) ? '0' : '1';
    }
}
