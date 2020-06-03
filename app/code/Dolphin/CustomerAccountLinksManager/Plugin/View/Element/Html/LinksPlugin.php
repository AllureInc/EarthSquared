<?php

namespace Dolphin\CustomerAccountLinksManager\Plugin\View\Element\Html;

use Closure;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Html\Links;
use Dolphin\CustomerAccountLinksManager\Helper\Data;
use Dolphin\CustomerAccountLinksManager\Model\Config\Source\Action;

class LinksPlugin
{
    protected $_helperData;

    public function __construct(
        Data $helperData
    ) {
        $this->_helperData = $helperData;
    }

	
    public function aroundRenderLink(Links $subject, Closure $proceed, AbstractBlock $link)
    {
        $output = $proceed($link);

        if ($this->_helperData->isEnabled() && $subject->getNameInLayout() == 'customer_account_navigation') {
            if (Action::EXCLUDE_SELECTED == $this->_helperData->getAction()) {
                if (in_array($link->getNameInLayout(), $this->_helperData->getSectionList())) {
                    return '';
                }
            } elseif (Action::INCLUDE_SELECTED == $this->_helperData->getAction()) {
                if (!in_array($link->getNameInLayout(), $this->_helperData->getSectionList())) {
                    return '';
                }
            }
        }

        return $output;
    }
}
