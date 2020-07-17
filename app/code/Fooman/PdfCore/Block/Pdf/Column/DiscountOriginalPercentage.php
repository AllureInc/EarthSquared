<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCore\Block\Pdf\Column;

use Fooman\PdfCore\Block\Pdf\Column;
use Fooman\PdfCore\Block\Pdf\ColumnInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class DiscountOriginalPercentage extends Column implements ColumnInterface
{
    const DEFAULT_WIDTH = 12;
    const DEFAULT_TITLE = 'Discount %';

    public function getGetter()
    {
        return [$this, 'getDiscountPercent'];
    }

    public function getDiscountPercent($row)
    {
        $orderItem = $this->getOrderItem($row);
        return $this->formatPercentage($this->calculateDiscountPercentage($orderItem));
    }

    public function calculateDiscountPercentage($orderItem)
    {
        $methodBaseOrigPrice = $this->convertInterfaceConstantToGetter(OrderItemInterface::BASE_ORIGINAL_PRICE);

        $baseRowTotal = $this->getValueViaConstant($orderItem, OrderItemInterface::BASE_ROW_TOTAL);
        $baseTax = $this->getValueViaConstant($orderItem, OrderItemInterface::BASE_TAX_AMOUNT);
        $baseTaxComp = $this->getValueViaConstant(
            $orderItem,
            OrderItemInterface::BASE_DISCOUNT_TAX_COMPENSATION_AMOUNT
        );
        $baseDiscount = $this->getValueViaConstant($orderItem, OrderItemInterface::BASE_DISCOUNT_AMOUNT);
        $baseWeee = $this->getValueViaConstant($orderItem, OrderItemInterface::BASE_WEEE_TAX_APPLIED_ROW_AMNT);

        $displayBaseRowTotal = $baseRowTotal + $baseTax + $baseTaxComp + $baseWeee - $baseDiscount;
        $qty = $this->getValueViaConstant($orderItem, \Magento\Sales\Api\Data\OrderItemInterface::QTY_ORDERED);

        if ($qty == 0) {
            return 0;
        }
        $actualUnitPricePaid = $displayBaseRowTotal/$qty;
        $origPrice = $orderItem->$methodBaseOrigPrice();

        if ($actualUnitPricePaid >= $origPrice || $origPrice == 0) {
            return 0;
        }

        return 100 * ($origPrice-$actualUnitPricePaid) / $origPrice;
    }

    public function formatPercentage($input)
    {
        return sprintf('%s %%', round($input));
    }

    private function getValueViaConstant($row, $constant)
    {
        return $row->{$this->convertInterfaceConstantToGetter($constant)}();
    }
}
