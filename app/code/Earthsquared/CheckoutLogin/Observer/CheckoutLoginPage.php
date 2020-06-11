<?php

namespace Earthsquared\CheckoutLogin\Observer;

class CheckoutLoginPage implements \Magento\Framework\Event\ObserverInterface {
	protected $_urlInterface;
	protected $resultRedirect;
	protected $request;

	public function __construct(
		\Magento\Customer\Model\Session $session,
		\Magento\Framework\UrlInterface $urlInterface,
		\Magento\Framework\Controller\ResultFactory $result,
		\Magento\Framework\HTTP\PhpEnvironment\Request $request
	) {
		$this->session = $session;
		$this->_urlInterface = $urlInterface;
		$this->request = $request;
		$this->resultRedirect = $result;
	}
	public function execute(\Magento\Framework\Event\Observer $observer) {
		//var_dump($this->request->getServer('QUERY_STRING'));
		//die;
		// print_r($this->request->getServer('QUERY_STRING'));
		// exit;
		if ($this->request->getServer('QUERY_STRING') == '' || $this->request->getServer('QUERY_STRING') == null) {
			if (!$this->session->isLoggedIn()) {
				$this->session->setAfterAuthUrl($this->_urlInterface->getCurrentUrl() . '?checkoutlogin=yes');
				$this->session->authenticate();
				return $this;
			}
		}

	}

}
