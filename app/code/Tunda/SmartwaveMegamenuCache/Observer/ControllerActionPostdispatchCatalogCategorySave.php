<?php
namespace Tunda\SmartwaveMegamenuCache\Observer;

use Magento\Framework\Event\ObserverInterface;

class ControllerActionPostdispatchCatalogCategorySave implements ObserverInterface
{

    protected $_helper;

    public function __construct(
        \Tunda\SmartwaveMegamenuCache\Helper\Data $helper
    ) {
        $this->_helper = $helper;

        return $this;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_helper->clearMenu();

    }
}
