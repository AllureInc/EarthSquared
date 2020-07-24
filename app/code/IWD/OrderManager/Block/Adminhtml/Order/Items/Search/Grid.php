<?php

namespace IWD\OrderManager\Block\Adminhtml\Order\Items\Search;

/**
 * Class Grid
 * @package IWD\OrderManager\Block\Adminhtml\Order\Items\Search
 */
class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid
{
    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'sales/order_create/loadBlock',
            ['block' => 'search_grid', '_current' => true, 'collapse' => null]
        );
    }

    public function getStore()
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $request = $objectManager->get('Magento\Framework\App\Request\Http');
            $currentUrl = $request->getServer('HTTP_REFERER');
            $orderDetails = explode('order_id',$currentUrl);
            $orderId  = str_replace('/','',end($orderDetails));

            $order = $objectManager->create('\Magento\Sales\Model\Order')
                ->load($orderId);

            return $order->getStore();
        }catch (\Exception $e){
            return $this->_sessionQuote->getStore();
        }
    }
}
