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
    protected $_quoteFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Session $session,
        \Magento\Framework\Pricing\Helper\Data $helperPrice,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
        $this->_session = $session;
        $this->helperPrice = $helperPrice;
        $this->_quoteFactory = $quoteFactory;
    }

    public function getDiscountAmount()
    {
        $quoteId = $this->_session->getQuote()->getId();        
        $q = $this->_quoteFactory->create()->load($quoteId);
        // echo "<pre>";
        // print_r($q->getData());
        // exit;
        //$totals = $this->cartTotalRepository->get($this->getQuote()->getId());
        // $quote = $this->_session->getQuote()->getId();
        // print_r($quote->getData());exit;
        // echo $totals->getDiscountAmount();
        // exit;
        $discountAmountQuote = $q->getBaseSubtotal() - $q->getBaseSubtotalWithDiscount();
        $discountAmount = $this->helperPrice->currency(abs($discountAmountQuote), true, false);
        return $discountAmount;
    }

}
