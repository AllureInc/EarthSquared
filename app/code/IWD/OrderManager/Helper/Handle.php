<?php

namespace IWD\OrderManager\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Handle extends AbstractHelper
{
    private $request;
    protected $scopeConfig;

    const IWD_LAYOUT = 'iwdordermanager_order_payment_form';

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    public function isAllow()
    {
        if($this->request->getFullActionName() && $this->request->getFullActionName() === self::IWD_LAYOUT){
            return true;
        }
        return false;
    }

    public function isVaultEnabled(){
        return $this->scopeConfig->getValue('payment/braintree_cc_vault/active',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}