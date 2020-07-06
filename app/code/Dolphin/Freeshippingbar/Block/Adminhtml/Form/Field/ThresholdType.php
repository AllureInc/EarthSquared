<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

class ThresholdType extends Select
{
    /**
     * @param Context  $context
     * @param Registry $coreRegistry
     * @param array    $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        array $data = []
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve customer group collection
     *
     * @return GroupCollection
     */
    public function getCustomerGroupCollection()
    {
        if (!$this->hasData('customer_group_collection')) {
            $collection = $this->groupCollectionFactory->create();
            $this->setData('customer_group_collection', $collection);
        }

        return $this->getData('customer_group_collection');
    }

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
            $this->setOptions($this->getCustomerGroup());
        }
        return parent::_toHtml();
    }

    public function getCustomerGroup()
    {
        $customerGroupArr = [];
        $customerGroupCollection = $this->getCustomerGroupCollection();
        if ($customerGroupCollection != '') {
            foreach ($customerGroupCollection as $customerGroup) {
                $customerGroupArr[] = ['label' => $customerGroup->getCode(), 'value' => $customerGroup->getCode()];
            }
            $customerGroupArr[] = ['label' => "All Groups", 'value' => "All Groups"];
        } else {
            $customerGroupArr[] = ['label' => 'NOT LOGGED IN', 'value' => 'NOT LOGGED IN'];
        }

        return $customerGroupArr;
    }
}
