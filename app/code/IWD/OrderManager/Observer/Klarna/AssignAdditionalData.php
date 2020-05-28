<?php
namespace IWD\OrderManager\Observer\Klarna;

use Klarna\Kp\Api\QuoteInterface;
use Klarna\Kp\Api\QuoteRepositoryInterface;
use Klarna\Kp\Model\Payment\Kp;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AssignData
 *
 * @package Klarna\Kp\Observer
 */
class AssignAdditionalData extends AbstractDataAssignObserver
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $klarnaQuoteRepository;
    /**
     * @var LoggerInterface
     */
    private $log;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var ScopeConfigInterface
     */
    private $config;
    
     /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * AssignData constructor.
     *
     * @param QuoteRepositoryInterface $klarnaQuoteRepository
     * @param LoggerInterface          $log
     * @param DataObjectFactory        $dataObjectFactory
     * @param ScopeConfigInterface     $config
     */
    public function __construct(
        LoggerInterface $log,
        DataObjectFactory $dataObjectFactory,
        ScopeConfigInterface $config,
         \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
         $this->objectManager = $objectManager;
        //hot fix
        if (class_exists('\Klarna\Kp\Api\QuoteRepositoryInterface')) {
            $this->klarnaQuoteRepository = $this->objectManager->create('\Klarna\Kp\Api\QuoteRepositoryInterface');
        }
        $this->log = $log;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->config = $config;
    }

    /**
     * Observer
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $payment = $this->readPaymentModelArgument($observer);
        if (false === strpos($payment->getMethod(), 'klarna')) {
            return;
        }

        $data = $this->readDataArgument($observer);
        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $additionalData = $this->dataObjectFactory->create(['data' => $additionalData]);

        $payment = $this->readPaymentModelArgument($observer);
        $quote = $payment->getQuote();
        if (!$this->config->isSetFlag(
            'payment/' . Kp::METHOD_CODE . '/active',
            ScopeInterface::SCOPE_STORES,
            $quote->getStore()
        )) {
            return;
        }
        try {
            /** @var QuoteInterface $klarnaQuote */
            $klarnaQuote = $this->klarnaQuoteRepository->getActiveByQuote($quote);
            $klarnaQuote->setAuthorizationToken($additionalData->getData('authorization_token'));
            $payment->setAdditionalInformation('method_title', $additionalData->getData('method_title'));
            $payment->setAdditionalInformation('logo', $additionalData->getData('logo'));
            $payment->setAdditionalInformation('method_code', $payment->getMethodInstance()->getCode());
            $payment->setAdditionalInformation('klarna_order_id', $klarnaQuote->getSessionId());
            $this->klarnaQuoteRepository->save($klarnaQuote);
        } catch (NoSuchEntityException $npe) {
            $data = ['klarna_id' => $additionalData->getData('authorization_token')];
            $this->log->error($npe, $data);
        }
    }
}