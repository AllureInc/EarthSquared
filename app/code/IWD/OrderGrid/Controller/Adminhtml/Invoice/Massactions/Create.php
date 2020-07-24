<?php

namespace IWD\OrderGrid\Controller\Adminhtml\Invoice\MassActions;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\InvoiceOrderInterface;
use IWD\OrderGrid\Controller\Adminhtml\AbstractMassAction;


class Create extends AbstractMassAction
{
    /**
     * @var string
     */
    protected $redirectUrl = 'sales/order/index';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var object
     */
    protected $collectionFactory;


    /**
     * @var InvoiceOrderInterface
     */
    protected $invoiceOrder;

    protected $orderRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param InvoiceOrderInterface $invoiceOrder
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        InvoiceOrderInterface $invoiceOrder,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        parent::__construct($context, $filter);
        $this->collectionFactory = $collectionFactory;
        $this->invoiceOrder = $invoiceOrder;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Return component referrer url
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: $this->redirectUrl;
    }


    /**
     * @param AbstractCollection $collection
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countInvoiceOrder = 0;
        $invoiceAlreadyExistsForOrder = 0;

        foreach ($collection->getItems() as $order) {
            try {
                $loadOrder = $this->orderRepository->get($order->getId());
                if(!$loadOrder->getInvoiceCollection()->count()){
                    $this->invoiceOrder->execute($order->getId());
                    $countInvoiceOrder++;
                }else{
                    $invoiceAlreadyExistsForOrder++;
                    break;
                }
            } catch (\Exception $e) {
                $message = '#%1 ' . $e->getMessage();
                $this->messageManager->addErrorMessage(__($message, $order->getIncrementId()));
            }

        }
        $countNonInvoiceOrder = $collection->count() - $countInvoiceOrder;

        if ($countNonInvoiceOrder && $countInvoiceOrder) {
            $this->messageManager->addErrorMessage(__('%1 order(s) cannot be invoice.', $countNonInvoiceOrder));
        } elseif ($countNonInvoiceOrder && !$invoiceAlreadyExistsForOrder) {
            $this->messageManager->addErrorMessage(__('You cannot invoice the order(s).'));
        }

        if ($countInvoiceOrder) {
            $this->messageManager->addSuccessMessage(__('We invoiced %1 order(s).', $countInvoiceOrder));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
       return $this->_authorization->isAllowed('Magento_Sales::invoice')
            && $this->_authorization->isAllowed('IWD_OrderGrid::iwdordergrid_invoice_create');

    }
}