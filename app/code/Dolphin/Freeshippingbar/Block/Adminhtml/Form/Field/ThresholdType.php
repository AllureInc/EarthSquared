<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

class ThresholdType extends Select
{
    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        return [
            ['label' => 'NOT LOGGED IN', 'value' => 'NOT LOGGED IN'],
            ['label' => 'General', 'value' => 'General'],
            ['label' => 'Wholesale', 'value' => 'Wholesale'],
            ['label' => 'Retailer', 'value' => 'Retailer'],
        ];
    }
}
