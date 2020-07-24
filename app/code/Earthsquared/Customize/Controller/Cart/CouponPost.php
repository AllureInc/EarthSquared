<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Earthsquared\Customize\Controller\Cart;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponPost extends \Magento\Checkout\Controller\Cart\CouponPost
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Quote\Model\Cart\CartTotalRepository $cartTotalRepository,
        \Magento\Framework\Pricing\Helper\Data $helperPrice,
        \Magento\Checkout\Model\Session $session,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $couponFactory,
            $quoteRepository
        );
        $this->resultJsonFactory = $resultJsonFactory;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->helperPrice = $helperPrice;
        $this->_session = $session;
        $this->_quoteFactory = $quoteFactory;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $couponCode = $this->getRequest()->getParam('remove') == 1
        ? ''
        : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();

        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            return $this->_goBack();
        }
        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }            
            if ($codeLength != 0) {
                //echo $cartQuote->getId();exit;
                $quoteId = $this->_session->getQuote()->getId();        
                $q = $this->_quoteFactory->create()->load($quoteId);
                $discountAmountQuote = $q->getBaseSubtotal() - $q->getBaseSubtotalWithDiscount();
                $discountAmount = $this->helperPrice->currency(abs($discountAmountQuote), true, false);
                //$totals = $this->cartTotalRepository->get($cartQuote->getId());                
                //$discountAmount = $this->helperPrice->currency(number_format(abs($totals->getDiscountAmount()), 2), true, false);
                $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                $coupon = $this->couponFactory->create();
                $coupon->load($couponCode, 'code');
                $store = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                if($store->getStore()->getWebsiteId() == 1)
                {
                    $labeled = "Promotional Code";
                } else {
                    $labeled = "Gift Voucher";
                }
                if (!$itemsCount) {
                    if ($isCodeLengthValid && $coupon->getId()) {
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();                        
                        $response = [
                            'errors' => false,
                            'message' => __(
                                $escaper->escapeHtml($labeled.' "%1" successfully applied. You received a discount of <span>%2</span>',['span']),
                                $escaper->escapeHtml($couponCode),
                                $discountAmount
                            ),
                        ];
                    } else {
                        $response = [
                            'errors' => true,
                            'message' => __(
                                'The '.$labeled.' "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            ),
                        ];
                    }
                } else {
                    if ($isCodeLengthValid && $coupon->getId() && $couponCode == $cartQuote->getCouponCode()) {
                        $response = [
                            'errors' => false,
                            'message' => __(
                                $escaper->escapeHtml($labeled.' "%1" successfully applied. You received a discount of <span>%2</span>',['span']),
                                $escaper->escapeHtml($couponCode),
                                $discountAmount
                            ),
                        ];
                    } else {
                        $response = [
                            'errors' => true,
                            'message' => __(
                                'The '.$labeled.' "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            ),
                        ];
                    }
                }
            } else {
                $response = [
                    'errors' => true,
                    'message' => __('You canceled the '.$labeled.'.'),
                ];
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage(),
            ];
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => __('We cannot apply the coupon code.'),
            ];
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
