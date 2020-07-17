<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCore\Block\System\Config;

class Colourpicker extends \Magento\Framework\Data\Form\Element\Text
{
    public function getHtml()
    {
        $this->addClass('hidden');
        $this->addClass('fooman-colour-picker');
        return parent::getHtml();
    }
}
