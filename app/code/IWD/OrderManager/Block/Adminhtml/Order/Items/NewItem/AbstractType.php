<?php

namespace IWD\OrderManager\Block\Adminhtml\Order\Items\NewItem;

use \IWD\OrderManager\Model\Quote\Item;
use \IWD\OrderManager\Block\Adminhtml\Order\Items\AbstractType as ItemsAbstract;

/**
 * Class AbstractType
 * @package IWD\OrderManager\Block\Adminhtml\Order\Items\NewItem
 */
class AbstractType extends ItemsAbstract
{
    /**
     * @param null $orderItem
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function getStockObjectForOrderItem($orderItem = null)
    {
        if ($orderItem == null) {
            $orderItem = $this->getOrderItem();
        }

        $productId = $orderItem->getProductId();
        $storeId = $orderItem->getStoreId();
        if ($orderItem->getProductType() == 'configurable') {
            /** @var Item $childQuoteItem */
            $childQuoteItem = $this->_objectManager
                ->create('\Magento\Quote\Model\Quote\Item')
                ->load($orderItem->getQuoteItemId(), 'parent_item_id');

            if (!empty($childQuoteItem)) {
                $productId = $childQuoteItem->getProductId();
                $storeId = $childQuoteItem->getStoreId();
            }
        }

        $stockItem = $this->stockRegistry->getStockItem(
            $productId,
            $storeId
        );

        return $stockItem;
    }

    /**
     * @param null $orderItem
     * @return float|int
     */
    public function getItemQty($orderItem = null)
    {
        if ($orderItem == null) {
            $orderItem = $this->getOrderItem();
        }

        $itemQty = $orderItem->getQtyOrdered();
        $itemQty = $itemQty < 0 ? 0 : $itemQty;

        $itemType = $orderItem->getProductType();
        $stockQty = $this->getStockQty();

        if($itemType == 'downloadable' && $itemQty != 0 || is_null($stockQty)){
            return $itemQty;
        }

        return $itemQty > $stockQty ? $stockQty : $itemQty;
    }

    /**
     * @return float
     */
    public function getStockQty()
    {
        return $this->getQty();
    }


    /**
     * @param null $orderItem
     * @return float
     */
    private function getQty($orderItem = null)
    {
        if (interface_exists(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $getProductSalable = $objectManager->create(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class);
            if (!$orderItem) {
                $orderItem = $this->getOrderItem();
            }
            $orderItem = $this->checkProductType($orderItem);

            if(!is_string($orderItem->getSku())){
                return $this->getStockObjectForOrderItem()->getQty();
            }

            return $getProductSalable->execute($orderItem->getSku(), $orderItem->getStoreId());
        }
        return $this->getStockObjectForOrderItem($orderItem)->getQty();
    }

    /**
     * @return string
     */
    public function getPrefixId()
    {
        return Item::PREFIX_ID;
    }

    /**
     * @return string
     */
    public function getEditedItemType()
    {
        return 'quote';
    }
}
