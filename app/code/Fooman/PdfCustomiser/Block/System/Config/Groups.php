<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCustomiser\Block\System\Config;

class Groups extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var \Magento\Customer\Model\Customer\Source\Group
     */
    private $groupSource;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Customer\Model\Customer\Source\Group $groupSource,
        array $data = []
    ) {
        $this->groupSource = $groupSource;
        parent::__construct($context, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration -- Magento 2 Core use
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getColumns());
        }
        return parent::_toHtml();
    }

    protected function getColumns()
    {
        $groups = $this->groupSource->toOptionArray();
        //groups with All Groups removed
        array_shift($groups);
        return $groups;
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
