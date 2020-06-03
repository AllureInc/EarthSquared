<?php
namespace Dolphin\CustomerAccountLinksManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ACTIVE = 'customeraccountlinksmanager/general/active';

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAction()
    {
        return $this->scopeConfig->getValue(
            'customeraccountlinksmanager/general/action',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getSectionList()
    {
        $list = $this->scopeConfig->getValue(
            'customeraccountlinksmanager/general/sections',
            ScopeInterface::SCOPE_STORE
        );

        return empty($list) ? [] : explode(',', $list);
    }
}
