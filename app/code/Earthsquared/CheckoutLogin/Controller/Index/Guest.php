<?php
namespace Earthsquared\CheckoutLogin\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Guest extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_urlInterface;
    protected $result;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface,
        ResultFactory $result,
        \Magento\Framework\View\Result\PageFactory $pageFactory) {
        $this->_pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        $this->resultRedirect = $result;
        return parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        // echo "<pre>";
        // print_r($post);
        // exit;

        $guestemail = $post['username'];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Session');
        $quote = $cart->getQuote();
        $quote->setCustomerId(null)
            ->setCustomerEmail($guestemail)
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
        $quote->save();
        //echo "<pre>";
        //print_r($quote->getCustomerEmail());
        // exit;
        $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        //$resultRedirect->setUrl('http://staging.earthsquared.com/checkout/?value=guest');
        $resultRedirect->setUrl($this->_storeManager->getStore()->getUrl('checkout') . '?value=guest');
        return $resultRedirect;
    }
}
