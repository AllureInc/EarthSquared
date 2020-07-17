<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCore\Block\Pdf\Column;

class SubtotalExcl extends \Fooman\PdfCore\Block\Pdf\Column implements \Fooman\PdfCore\Block\Pdf\ColumnInterface
{
    const DEFAULT_WIDTH = 12;
    const DEFAULT_TITLE = 'Subtotal (Excl.)';
    const COLUMN_TYPE = 'fooman_currency';

    public function getGetter()
    {
        return [$this, 'getSubtotal'];
    }

    public function getSubtotal($row)
    {
        $property = \Magento\Sales\Api\Data\OrderItemInterface::ROW_TOTAL;
        $baseProperty = \Magento\Sales\Api\Data\OrderItemInterface::BASE_ROW_TOTAL;

        $subtotal = $row->{$this->convertInterfaceConstantToGetter($property)}();
        $baseSubtotal = $row->{$this->convertInterfaceConstantToGetter($baseProperty)}();

        if ($this->isDisplayingBothCurrencies()) {
            return [
                ['currency' => $this->getBaseCurrencyCode(), 'amount' => $baseSubtotal],
                ['currency' => $this->getCurrencyCode(), 'amount' => $subtotal]
            ];
        }

        if ($this->getUseOrderCurrency()) {
            if ($subtotal) {
                return [['currency' => $this->getCurrencyCode(), 'amount' => $subtotal]];
            }
            return $subtotal;
        }
        if ($baseSubtotal) {
            return [['currency' => $this->getBaseCurrencyCode(), 'amount' => $baseSubtotal]];
        }
        return $baseSubtotal;
    }
}
