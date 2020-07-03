<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Block;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class Shippingbar extends \Magento\Framework\View\Element\Template
{

    protected $HelperData;
    protected $_storeManager;
    protected $_customerGroupCollection;
    protected $_customerSession;
    protected $_cart;
    protected $_checkoutSession;
    protected $currencyCode;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        StoreManagerInterface $storeConfig,
        \Dolphin\Freeshippingbar\Helper\Data $HelperData,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Group $customerGroupCollection,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Request\Http $request,
        CurrencyFactory $currencyFactory,
        array $data = []
    ) {
        $this->_objectManager = $objectmanager;
        $this->storeConfig = $storeConfig;
        $this->formKey = $formKey;
        $this->HelperData = $HelperData;
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_customerGroupCollection = $customerGroupCollection;
        $this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->currencyCode = $currencyFactory->create();
        parent::__construct($context, $data);
    }

    /**
     * @var string
     */
    protected $_template = 'Dolphin_Freeshippingbar::shippingbar.phtml';

    public function getActionName()
    {
        return $this->_request->getFullActionName();
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getBaseUrl()
    {
        $storeManager = $this->_objectManager->get(StoreManagerInterface::class);
        return $storeManager->getStore()->getBaseUrl();
    }

    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    public function getCustomerGroup()
    {
        $currentGroupId = $this->_customerSession->getCustomer()->getGroupId();
        $collection = $this->_customerGroupCollection->load($currentGroupId);
        return $collection->getCustomerGroupCode();
    }

    public function getCurrencySymbol()
    {
        $currentCurrency = $this->storeConfig->getStore()->getCurrentCurrencyCode();
        $currency = $this->currencyCode->load($currentCurrency);
        return $currency->getCurrencySymbol();
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getThreshold()
    {
        return $this->HelperData->getThreshold();
    }

    public function getSucessMessage()
    {
        return $this->HelperData->getSucessMessage();
    }

    public function getSticky()
    {
        return $this->HelperData->getSticky();
    }

    public function getBarPosition()
    {
        return $this->HelperData->getBarPosition();
    }

    public function getCloseButton()
    {
        return $this->HelperData->getCloseButton();
    }

    public function getcalCulateSubtotal()
    {
        return $this->HelperData->getcalCulateSubtotal();
    }

    public function getDisplayBarIn()
    {
        return $this->HelperData->getDisplayBarIn();
    }

    public function getDisplayOn()
    {
        return $this->HelperData->getDisplayOn();
    }

    public function getBarSize()
    {
        return $this->HelperData->getBarSize();
    }

    public function getLoadDelayTime()
    {
        return $this->HelperData->getLoadDelayTime();
    }

    public function getHideAfter()
    {
        return $this->HelperData->getHideAfter();
    }

    public function getFontFamily()
    {
        return $this->HelperData->getFontFamily();
    }

    public function getTextAlign()
    {
        return $this->HelperData->getTextAlign();
    }

    public function getFontColor()
    {
        return $this->HelperData->getFontColor();
    }

    public function getShippingPriceColor()
    {
        return $this->HelperData->getShippingPriceColor();
    }

    public function getCloseButtonColor()
    {
        return $this->HelperData->getCloseButtonColor();
    }

    public function getBgColor()
    {
        return $this->HelperData->getBgColor();
    }

    public function getAnimationEffect()
    {
        return $this->HelperData->getAnimationEffect();
    }

    public function getCustomCss()
    {
        return $this->HelperData->getCustomCss();
    }

    public function getCart()
    {
        return $this->_cart;
    }

    public function getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
}
