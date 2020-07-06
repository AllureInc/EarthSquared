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
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Catched event succssfully');
		
        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment()->getMethod();
        $comment = '';
		$logger->info('sales place-'.$payment);
		if($payment != 'braintree' && $payment != 'paypal_express'){
			$customer_group = $order->getCustomerGroupId();
			//$websiteid = $order->getStore()->getWebsiteId();
			$customer_group_array = array(1, 5, 7, 8, 9, 10, 11, 17, 18, 19, 20, 21, 23, 24, 25);
			$is_customer_group = array_search($customer_group,$customer_group_array);
			if($is_customer_group != '' && $payment == 'checkmo'){ 
				$order->setState("new")->setStatus("pending");
				$order->addStatusToHistory($order->getStatus(), $comment, false);
			}
			if($customer_group == 2){
				$order->setState("processing")->setStatus("pro_forma");
				$order->addStatusToHistory($order->getStatus(), $comment, false);
			}
		}/*else{
			$order->setState("processing")->setStatus("paid_order_received");
			$order->addStatusToHistory($order->getStatus(), $comment, false);
		}*/
		$order->save();
		
    }
}

