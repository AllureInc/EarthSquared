<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Earthsquared\Customize\Block\Cart;

/**
 * Block with apply-coupon form.
 *
 * @api
 * @since 100.0.2
 */
class Coupon extends \Magento\Checkout\Block\Cart\Coupon
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\Cart\CartTotalRepository $cartTotalRepository,
        \Magento\Framework\Pricing\Helper\Data $helperPrice,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->helperPrice = $helperPrice;
    }

    public function getDiscountAmount()
    {
        $totals = $this->cartTotalRepository->get($this->getQuote()->getId());
        $discountAmount = $this->helperPrice->currency(number_format(abs($totals->getDiscountAmount()), 2), true, false);
        return $discountAmount;
    }

}
