<?php

namespace Dolphin\OrderStatus\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class SalesOrderShipmentAfter implements ObserverInterface
{
	const ORDER_STATE = 'processing';
	const ORDER_STATUS = 'dispatched_ship';
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $order->setState(self::ORDER_STATE)->setStatus(self::ORDER_STATUS);
        $comment = '';
        $order->addStatusToHistory(self::ORDER_STATUS, $comment);
        $order->save();
    }
}
