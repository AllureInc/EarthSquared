<?php
namespace Earthsquared\Customize\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $_pageFactory;
    protected $wishlist;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Framework\View\Result\PageFactory $pageFactory) {
        $this->_pageFactory = $pageFactory;
        $this->wishlist = $wishlist;
        return parent::__construct($context);
    }

    public function execute()
    {
        $productId = $_REQUEST['pid'];
        $customerId = $_REQUEST['cid'];
        $wish = $this->wishlist->loadByCustomerId($customerId);
        $items = $wish->getItemCollection();
        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($items as $item) {
            if ($item->getProductId() == $productId) {
                $item->delete();
                $wish->save();
            }
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
