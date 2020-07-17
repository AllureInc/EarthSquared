<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCore\Block\Pdf\Column;

class NameAndSku extends \Fooman\PdfCore\Block\Pdf\Column implements \Fooman\PdfCore\Block\Pdf\ColumnInterface
{
    const DEFAULT_WIDTH = 20;
    const DEFAULT_TITLE = 'Product';
    const COLUMN_TYPE = 'fooman_textWithBreak';

    public function getGetter()
    {
        return [$this, 'getNameAndSku'];
    }

    public function getNameAndSku($row)
    {
        $orderItem = $this->getOrderItem($row);
        return $orderItem->getName() . '<br/>' . $orderItem->getSku();
    }
}
