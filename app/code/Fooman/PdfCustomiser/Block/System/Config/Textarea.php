<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCustomiser\Block\System\Config;

class Textarea extends \Magento\Framework\View\Element\AbstractBlock
{
    public function setId($elementId)
    {
        $this->setData('id', $elementId);
        return $this;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration -- Magento 2 core use
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        return '<textarea name="' .
            $this->getInputName() .
            '" id="' .
            $this->getInputId() .
            '" class="' .
            $this->getClass() .
            '" title="' .
            $this->escapeHtml($this->getTitle()) .
            '" rows="3" cols="40" '.
            $this->getExtraParams() .
            '>' .
            $this->getValue().
            '</textarea>';
    }
}
