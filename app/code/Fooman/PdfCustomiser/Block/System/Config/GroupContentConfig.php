<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCustomiser\Block\System\Config;

class GroupContentConfig extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    private $groupsRenderer;
    private $textAreaRenderer;

    /**
     * Prepare to render
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration -- Magento 2 Core use
    protected function _prepareToRender()
    {
        $this->addColumn(
            'group_id',
            [
                'label'    => __('Group'),
                'renderer' => $this->getGroupsRenderer()
            ]
        );
        $this->addColumn(
            'content',
            [
                'label' => __('Content'),
                'renderer'=> $this->getTextAreaRenderer()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Group Content');
    }

    /**
     * provide hash of current value so it gets preselected
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration -- Magento 2 Core use
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getGroupsRenderer()->calcOptionHash($row->getData('group_id'))]
            = 'selected="selected"';
        $row->setData('option_extra_attrs', $optionExtraAttr);
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getGroupsRenderer()
    {
        if (null === $this->groupsRenderer) {
            $element = $this->getElement();
            $uniqId = hash('sha256', $element->getHtmlId() . $element->getScope() . $element->getScopeId());
            $this->groupsRenderer = $this->getLayout()->createBlock(
                Groups::class,
                'fooman_pdfcustomiser_system_group_selector_' . $uniqId,
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->groupsRenderer;
    }

    private function getTextAreaRenderer()
    {
        if (null === $this->textAreaRenderer) {
            $element = $this->getElement();
            $uniqId = hash('sha256', $element->getHtmlId() . $element->getScope() . $element->getScopeId());
            $this->textAreaRenderer = $this->getLayout()->createBlock(
                Textarea::class,
                'fooman_pdfcustomiser_system_group_content_' . $uniqId
            );
        }
        return $this->textAreaRenderer;
    }

    public function getHtmlId()
    {
        return $this->getElement()->getHtmlId();
    }
}
