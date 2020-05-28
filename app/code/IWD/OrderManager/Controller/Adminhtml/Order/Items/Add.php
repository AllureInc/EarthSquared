<?php

namespace IWD\OrderManager\Controller\Adminhtml\Order\Items;

use IWD\OrderManager\Model\Order\Converter;
use IWD\OrderManager\Model\Order\Order;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ProductFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class Add
 * @package IWD\OrderManager\Controller\Adminhtml\Order\Items
 */
class Add extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'IWD_OrderManager::iwdordermanager_items_add';

    /**
     * @var Order
     */
    private $order;

    /**
     * @var Converter
     */
    private $orderConverter;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var DataObject
     */
    private $dataObject;

    /**
     * @var ProductFactory
     */
    private $product;


    private $childrenProduct;


    protected $_mediaDirectory;


    protected $_fileUploaderFactory;


    protected $customOptions;

    /**
     * Add constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Converter $orderConverter
     * @param Order $order
     * @param DataObject $dataObject
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Converter $orderConverter,
        Order $order,
        DataObject $dataObject,
        ProductFactory $product,
        Configurable $childrenProduct,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Catalog\Model\Product\Option $customOptions
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->orderConverter = $orderConverter;
        $this->order = $order;
        $this->dataObject = $dataObject;
        $this->product = $product;
        $this->childrenProduct = $childrenProduct;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->customOptions = $customOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $response = [
                'result' => $this->prepareResultHtml(),
                'status' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'error' => $e->getMessage(),
                'status' => false
            ];
        }

        $updateResult = $this->dataObject->addData($response);
        $this->_session->setIwdOmAddedItemsResult($updateResult);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('iwdordermanager/order_items/addResult');
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function prepareResultHtml()
    {
        $resultPage = $this->resultPageFactory->create();
        $product = $this->product->create();
        /**
         * @var $formContainer \IWD\OrderManager\Block\Adminhtml\Order\Items\NewItem\Form
         */
        $formContainer = $resultPage->getLayout()->getBlock('iwdordermamager_order_items_form_container');
        if (empty($formContainer)) {
            throw new LocalizedException(__('Can not load block'));
        }

        $order = $this->getOrder();
        $getNewOrderItems = $this->getNewOrderItems();
        $errors = $this->orderConverter->getErrors();

        foreach ($getNewOrderItems as $key => $item){
            $sku = $product->load($item->getProductId())->getSku();
            if($sku != $item->getSku()){
                $item->setSku($sku);
                $orderItems[$key] = $item;
            }else{
                $orderItems[$key] = $item;
            }
        }

        if(empty($orderItems)){
            throw new LocalizedException(__('We can\'t add product(s). You need to check qty and stock status of product'));
        }

        $formContainer->setOrder($order);
        $formContainer->setNewOrderItems($orderItems);
        $formContainer->setErrors($errors);

        return $formContainer->toHtml();
    }

    /**
     * @return Order
     * @throws \Exception
     */
    private function getOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $this->order->load($id);
        if (!$this->order->getEntityId()) {
            throw new LocalizedException(__('Can not load order'));
        }
        return $this->order;
    }

    /**
     * @return \IWD\OrderManager\Model\Order\Item[]
     */

    private function _upload($key,$name,$fileExtension){
        try{
            $filePath = 'custom_options/quote/'.substr($name,0, 1).'/'.substr($name,1, 1).'/';
            $target = $this->_mediaDirectory->getAbsolutePath($filePath);
            $uploader = $this->_fileUploaderFactory->create(['fileId' => $key]);
            if($fileExtension){
                $uploader->setAllowedExtensions($fileExtension);
            }
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
            throw new LocalizedException(__('File type is not correct'));
        }
    }


    public function getFileExtension($productId,$optionId){
        try{
            $fileExtension = array();
            $product = $this->product->create()->load($productId);
            $customOptions = $this->customOptions->getProductOptionCollection($product);
            foreach($customOptions as $option){
                if($option['option_id'] == $optionId && isset($option['file_extension']) && $option['file_extension'] != ''){
                    $fileExtension = str_replace(' ','',trim($option['file_extension']));
                    $fileExtension = explode(',',$fileExtension);
                    return $fileExtension;
                }
            }

            return false;

        }catch(\Exception $e){
            return false;
        }
    }

    private function getNewOrderItems()
    {
        $items = $this->getRequest()->getParam('item', []);
        $files = $this->getRequest()->getFiles();

        if(gettype($files) == 'object' && count($files) > 0){
            foreach($files as $key => $file){
                if($file['size'] == 0){continue;}
                $keyArray = explode('_',$key);
                $keyId = $keyArray['1'];
                $keyOption = $keyArray['3'];
                $fileExtension = $this->getFileExtension($keyId,$keyOption);
                $result = $this->_upload($key,strtolower($file['name']),$fileExtension);
                if(empty($result)){continue;}
                $items[$keyId]['options'][$keyOption] = $result;
            }
        }

        $order = $this->getOrder();

        return $this->orderConverter->createNewOrderItems($items, $order);
    }
}