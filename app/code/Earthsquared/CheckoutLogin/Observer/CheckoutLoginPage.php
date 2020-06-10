<?php

namespace Earthsquared\CheckoutLogin\Observer;

class CheckoutLoginPage implements \Magento\Framework\Event\ObserverInterface
{
    protected $_urlInterface;
    protected $resultRedirect;

    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Controller\ResultFactory $result
    ) {
        $this->session = $session;
        $this->_urlInterface = $urlInterface;
        $this->resultRedirect = $result;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!isset($_REQUEST['value'])) {
            if (!$this->session->isLoggedIn()) {
                $this->session->setAfterAuthUrl($this->_urlInterface->getCurrentUrl() . '?checkoutlogin=yes');
                $this->session->authenticate();
                return $this;
            }
        }
    }
}
