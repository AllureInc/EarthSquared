<?php

namespace Dolphin\Freeshippingbar\Block\Adminhtml\Form\Field;

use Dolphin\Freeshippingbar\Block\Adminhtml\Form\Field\ThresholdType;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Html\Date;

/**
 * Class Ranges
 */
class Threshold extends AbstractFieldArray
{
    private $ThresholdRenderer;
    private $dateRenderer;

    /**
     * @param Context  $context
     * @param Registry $coreRegistry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('customer_group', [
            'label' => __('Customer Group'),
            'renderer' => $this->getThresholdTypeRenderer(),
        ]);
        $this->addColumn('threshold_value', [
            'label' => __('Threshold Value'),
        ]);
        $this->addColumn('notification', [
            'label' => __('Notification Text'),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $threshold = $row->getThreshold();
        if ($threshold !== null) {
            $options['option_' . $this->getThresholdTypeRenderer()->calcOptionHash($threshold)] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @return PeriodDateType
     * @throws LocalizedException
     */
    private function getThresholdTypeRenderer()
    {
        if (!$this->ThresholdRenderer) {
            $this->ThresholdRenderer = $this->getLayout()->createBlock(
                ThresholdType::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->ThresholdRenderer;
    }
}
