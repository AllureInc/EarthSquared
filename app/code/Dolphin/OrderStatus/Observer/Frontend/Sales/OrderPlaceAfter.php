<?php

namespace Dolphin\OrderStatus\Observer\Frontend\Sales;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
		/*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Catched event succssfully');*/
		
        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment()->getMethod();
		if($payment != 'braintree' && $payment != 'paypal_express'){
			$customer_group = $order->getCustomerGroupId();
			//$websiteid = $order->getStore()->getWebsiteId();
			if($customer_group == 1){ 
				$order->setState("pending")->setStatus("pending");
			}
			if($customer_group == 2){
				$order->setState("pro_forma")->setStatus("pro_forma");	
			}
		}
		
    }
}

