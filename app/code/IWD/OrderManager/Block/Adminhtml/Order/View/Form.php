<?php

namespace IWD\OrderManager\Block\Adminhtml\Order\View;

use Magento\Backend\Model\Session\Quote;
use Magento\Braintree\Gateway\Config\Config as GatewayConfig;
use Magento\Braintree\Model\Adminhtml\Source\CcType;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Config;
use IWD\OrderManager\Helper\Handle;

class Form extends \Magento\Braintree\Block\Form
{
    private $handle;

    protected $sessionQuote;

    public function __construct(
        Context $context,
        Config $paymentConfig,
        Quote $sessionQuote,
        GatewayConfig $gatewayConfig,
        CcType $ccType,
        Data $paymentDataHelper,
        Handle $handle,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig,$sessionQuote,$gatewayConfig,$ccType,$paymentDataHelper,$data);
        $this->handle = $handle;
    }
    public function isVaultEnabled()
    {
        if($this->handle->isAllow() || !$this->handle->isVaultEnabled()){
            return false;
        }

        return true;
    }
}