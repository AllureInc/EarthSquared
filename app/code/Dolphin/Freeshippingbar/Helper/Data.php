<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Helper;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Serialize\SerializerInterface;

class Data extends AbstractHelper
{

    protected $scopeConfig;
    private $serializer;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        SerializerInterface $serializer,
        Session $session,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->_checkoutSession = $session;
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return $Threshold
     */
    public function getThreshold()
    {
        $Threshold = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/display_setting/threshold',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $additionalData = $this->serializer->unserialize($Threshold);

        return $additionalData;
    }

    /**
     * @return $SucessMessage
     */
    public function getSucessMessage()
    {
        $SucessMessage = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/display_setting/sucess_message',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $SucessMessage;
    }

    /**
     * @return $BarPosition
     */
    public function getBarPosition()
    {
        $BarPosition = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/display_setting/bar_position',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $BarPosition;
    }

    /**
     * @return $Sticky
     */
    public function getSticky()
    {
        $Sticky = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/display_setting/sticky',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $Sticky;
    }

    /**
     * @return $CloseButton
     */
    public function getCloseButton()
    {
        $CloseButton = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/display_setting/close_button',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $CloseButton;
    }

    /**
     * @return $CulateSubtotal
     */
    public function getcalCulateSubtotal()
    {
        $CulateSubtotal = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/display_setting/calculate_subtotal',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $CulateSubtotal;
    }

    /**
     * @return $DisplayBarIn
     */
    public function getDisplayBarIn()
    {
        $DisplayBarIn = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/display_setting/display_bar_in',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $DisplayBarIn;
    }

    /**
     * @return $DisplayOn
     */
    public function getDisplayOn()
    {
        $DisplayOn = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/display_setting/display_on',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $DisplayOn;
    }

    /**
     * @return $BarSize
     */
    public function getBarSize()
    {
        $BarSize = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/bar_size',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $BarSize;
    }

    /**
     * @return $LoadDelayTime
     */
    public function getLoadDelayTime()
    {
        $LoadDelayTime = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/load_delay_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $LoadDelayTime;
    }

    /**
     * @return $HideAfter
     */
    public function getHideAfter()
    {
        $HideAfter = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/hide_after',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $HideAfter;
    }

    /**
     * @return $FontFamily
     */
    public function getFontFamily()
    {
        $FontFamily = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/font_family',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $FontFamily;
    }

    /**
     * @return $TextAlign
     */
    public function getTextAlign()
    {
        $TextAlign = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/text_align',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $TextAlign;
    }

    /**
     * @return $FontColor
     */
    public function getFontColor()
    {
        $FontColor = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/font_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $FontColor;
    }

    /**
     * @return $ShippingPriceColor
     */
    public function getShippingPriceColor()
    {
        $ShippingPriceColor = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/shipping_price_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $ShippingPriceColor;
    }

    /**
     * @return $CloseButtonColor
     */
    public function getCloseButtonColor()
    {
        $CloseButtonColor = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/close_button_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $CloseButtonColor;
    }

    /**
     * @return $BgColor
     */
    public function getBgColor()
    {
        $BgColor = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/bg_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $BgColor;
    }

    /**
     * @return $AnimationEffect
     */
    public function getAnimationEffect()
    {
        $AnimationEffect = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/animation_effect',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $AnimationEffect;
    }

    /**
     * @return $CustomCss
     */
    public function getCustomCss()
    {
        $CustomCss = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/bar_design_setting/custom_css',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $CustomCss;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $Enabled = $this->scopeConfig->getValue(
            'dolphin_freeshippingbar/general_setting/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $Enabled;
    }
}
