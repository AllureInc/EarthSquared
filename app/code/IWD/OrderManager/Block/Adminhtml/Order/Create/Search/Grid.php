<?php
namespace IWD\OrderManager\Block\Adminhtml\Order\Create\Search;

class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Search\Grid
{

    protected $_sessionQuote;

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
            if(is_null($order->getEntityId())){
                return $this->_sessionQuote->getStore();
            }
            return $order->getStore();
        }catch (\Exception $e){
            return $this->_sessionQuote->getStore();
        }
    }
}
?>
