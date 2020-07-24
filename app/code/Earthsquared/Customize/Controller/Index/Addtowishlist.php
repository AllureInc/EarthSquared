<?php
namespace Earthsquared\Customize\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Addtowishlist extends Action {

    protected $customerSession;
    protected $wishlistRepository;
    protected $productRepository;
    public $_storeManager;

    public function __construct(
    Context $context,
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
    \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
    ResultFactory $resultFactory,
    \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
    \Magento\Store\Model\StoreManagerInterface $storeManager
        ) {
        $this->customerSession = $customerSession;
        $this->wishlistRepository= $wishlistRepository;
        $this->productRepository = $productRepository;
        $this->resultFactory = $resultFactory;
        $this->jsonFactory = $jsonFactory;
        $this->_storeManager=$storeManager;
        parent::__construct($context);
    }

    public function execute() {
        $customerId = $this->customerSession->getCustomer()->getId();

        if(!$customerId) {
           $jsonData = ['result' => ['status' => 200, 'redirect' => 1,'message' => 'Customer not logged in.']]; 
            $result = $this->jsonFactory->create()->setData($jsonData);
            return $result;
        }
        $productId = $this->getRequest()->getParam('productId');

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        $wishlist = $this->wishlistRepository->create()->loadByCustomerId($customerId, true);
       
        $wishlist->addNewItem($product);
        $wishlist->save();
        $wishlist->getItemCollection();
        $wishlistproductCount = count($wishlist);  

        $jsonData = ['result' => ['status' => 200, 'redirect' => 0, 'message' => __(' has been added to your Wish List.'), 'wishcount' => $wishlistproductCount]];
        $result = $this->jsonFactory->create()->setData($jsonData);
        return $result;
    }
}