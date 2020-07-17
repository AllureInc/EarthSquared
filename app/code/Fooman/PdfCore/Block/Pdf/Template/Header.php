<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCore\Block\Pdf\Template;

class Header extends \Fooman\PdfCore\Block\Pdf\Block
{
    // phpcs:ignore PSR2.Classes.PropertyDeclaration -- Magento 2 Core use
    protected $_template = 'Fooman_PdfCore::pdf/header.phtml';
}
