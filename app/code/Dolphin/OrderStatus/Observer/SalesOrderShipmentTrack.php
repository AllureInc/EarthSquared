<?php

namespace Dolphin\OrderStatus\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentTrack implements ObserverInterface
{
    const ORDER_STATE = 'processing';
    const ORDER_STATUS = 'dispatched_ship';

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $track = $observer->getEvent()->getTrack();
        $order = $this->orderRepository->get($track->getOrderId());
        $order->setState(self::ORDER_STATE)->setStatus(self::ORDER_STATUS);
        $comment = '';
        $order->addStatusToHistory(self::ORDER_STATUS, $comment);
        $order->save();
    }
}
