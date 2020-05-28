<?php

namespace IWD\OrderManager\Plugin\Order\Braintree;

class Cc
{
    protected $request;
    protected $braintreeAdapter;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Braintree\Model\Adapter\BraintreeAdapterFactory $braintreeAdapter
    ) {
        $this->request = $request;
        $this->braintreeAdapter = $braintreeAdapter;
    }

    public function beforeToHtml(\Magento\Payment\Block\Form\Cc $block)
    {

        if($block->getMethodCode() === 'braintree'){
            $block->setTemplate('Magento_Braintree::form/cc.phtml');
        }

    }
}