<?php

namespace IWD\MultiInventory\Block\Adminhtml\Warehouses\Product\Grid\Render;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\DataObject;
use Magento\Backend\Block\Context;

/**
 * Class DefaultQty
 * @package IWD\MultiInventory\Block\Adminhtml\Warehouses\Product\Grid\Render
 */
class DefaultQty extends AbstractRenderer
{
    /**
     * DefaultQty constructor.
     * @param Context $context
     * @param array $data
     * @param GetProductSalableQtyInterface $getProductSalableQty
     */
    public function __construct(
        Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render(DataObject $row)
    {
        $productId = $row->getData('entity_id');

        $stockId = str_replace('qty', '', $this->getColumn()->getIndex());
        $qty = $row->getData($this->getColumn()->getIndex()) * 1;
        $salableQuantity = $this->getQty($row);
        $isInStock = $row->getData('is_in_stock_' . $stockId) ? 'checked="checked"' : '';
        $classInputQty = (Stock::DEFAULT_STOCK_ID == $stockId) ? 'inventory_qty_default' : 'inventory_qty';

        $qtyInput = '';
        $salableQuantityText = '';

        if (!in_array($row->getData('type_id'), ['configurable', 'bundle', 'grouped'])) {
            $qtyInput = sprintf(
                "<input class='product-qty input-text admin__control-text %s' type='text' name='stock[%s][%s][qty]' value='%s' title='Qty'/>",
                $classInputQty,
                $productId,
                $stockId,
                $qty
            );
            if ($salableQuantity) {
                $salableQuantityText = "<span>&nbsp;&nbsp;&nbsp;" . 'Salable Quantity:' . " {$salableQuantity}</span>";
            }
        }

        return sprintf(
            "<div class='product-stock-cell'>
                %s
                <div class='product-in-stock' title='In Stock'>
                    <input class='product_in_stock admin__control-checkbox' type='checkbox' %s name='stock[%s][%s][is_in_stock]' value='1'/>
                    <label></label>
                </div>
            </div>%s",
            $qtyInput,
            $isInStock,
            $productId,
            $stockId,
            $salableQuantityText
        );
    }

    /**
     * Backward Compatibility
     * @param $row
     * @return mixed
     */
    private function getQty($row)
    {
        if (interface_exists(Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $getProductSalable = $objectManager->create(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class);
            $stockId = str_replace('qty', '', $this->getColumn()->getIndex());
            return $getProductSalable->execute($row->getData('sku'), $stockId);
        }
        return $row->getData($this->getColumn()->getIndex()) * 1;
    }


}
