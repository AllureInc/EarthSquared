<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCore\Block\Pdf\Column;

class Discount extends \Fooman\PdfCore\Block\Pdf\Column implements \Fooman\PdfCore\Block\Pdf\ColumnInterface
{
    const DEFAULT_WIDTH = 12;
    const DEFAULT_TITLE = 'Discount';
    const COLUMN_TYPE = 'fooman_currency';

    public function getGetter()
    {
        return [$this, 'getDiscount'];
    }

    public function getDiscount($row)
    {
        $property = \Magento\Sales\Api\Data\OrderItemInterface::DISCOUNT_AMOUNT;
        $baseProperty = \Magento\Sales\Api\Data\OrderItemInterface::BASE_DISCOUNT_AMOUNT;

        $discount = $row->{$this->convertInterfaceConstantToGetter($property)}();
        $baseDiscount = $row->{$this->convertInterfaceConstantToGetter($baseProperty)}();

        if ($this->isDisplayingBothCurrencies()) {
            return [
                ['currency' => $this->getBaseCurrencyCode(), 'amount' => $baseDiscount],
                ['currency' => $this->getCurrencyCode(), 'amount' => $discount]
            ];
        }
        if ($this->getUseOrderCurrency()) {
            if ($discount) {
                return [['currency' => $this->getCurrencyCode(), 'amount' => $discount]];
            }
            return $discount;
        }
        if ($baseDiscount) {
            return [['currency' => $this->getBaseCurrencyCode(), 'amount' => $baseDiscount]];
        }
        return $baseDiscount;
    }
}
