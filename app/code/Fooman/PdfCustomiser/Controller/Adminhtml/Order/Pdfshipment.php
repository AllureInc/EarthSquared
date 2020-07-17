<?php

namespace Fooman\PdfCustomiser\Controller\Adminhtml\Order;

use Fooman\PdfCustomiser\Model\ControllerConfig;
use Magento\Framework\Controller\ResultFactory;

/**
 * @author     Kristof Ringleff
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Pdfshipment extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    /**
     * @var \Fooman\PdfCustomiser\Block\ShipmentFactory
     */
    private $shipmentDocumentFactory;

    /**
     * @var \Fooman\PdfCustomiser\Block\OrderShipmentFactory
     */
    private $orderShipmentDocumentFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ControllerConfig
     */
    private $controllerConfig;

    /**
     * @var \Fooman\PdfCore\Model\PdfFileHandling
     */
    private $pdfFileHandling;

    /**
     * @var \Fooman\PdfCore\Model\PdfRenderer
     */
    private $pdfRenderer;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Fooman\PdfCore\Model\PdfFileHandling $pdfFileHandling,
        \Fooman\PdfCore\Model\PdfRenderer $pdfRenderer,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Fooman\PdfCustomiser\Block\ShipmentFactory $shipmentDocumentFactory,
        \Fooman\PdfCustomiser\Block\OrderShipmentFactory $orderShipmentDocumentFactory,
        ControllerConfig $controllerConfig
    ) {
        $this->pdfFileHandling = $pdfFileHandling;
        $this->pdfRenderer = $pdfRenderer;
        $this->orderRepository = $orderRepository;
        $this->shipmentDocumentFactory = $shipmentDocumentFactory;
        $this->orderShipmentDocumentFactory = $orderShipmentDocumentFactory;
        $this->controllerConfig = $controllerConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');

        if ($orderId) {
            $order = $this->orderRepository->get($orderId);
            if ($order) {
                $this->processOrder($order);
            }
        }

        if ($this->pdfRenderer->hasPrintContent()) {
            return $this->pdfFileHandling->sendPdfFile($this->pdfRenderer);
        }

        $this->messageManager->addErrorMessage(__('No shipment to print.'));
        $this->messageManager->addNoticeMessage(
            __(
                'Enable Stores > Configuration > Pdf-Printouts > Shipment > Print Order as Packing Slip ' .
                'to print orders of any status.'
            )
        );
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath($this->getRedirectUrl($orderId));
    }

    /**
     * @param $orderId
     *
     * @return string
     */
    private function getRedirectUrl($orderId)
    {
        if ($orderId) {
            return 'sales/order/view/order_id/' . $orderId;
        }
        return 'sales/order/index';
    }

    /**
     * @param $order
     */
    public function processOrder($order)
    {
        if ($this->controllerConfig->shouldPrintOrderAsPackingSlip()) {
            $document = $this->orderShipmentDocumentFactory->create(
                ['data' => ['order' => $order]]
            );

            $this->pdfRenderer->addDocument($document);
        } else {
            $shipments = $order->getShipmentsCollection();
            if ($shipments) {
                foreach ($shipments as $shipment) {
                    $document = $this->shipmentDocumentFactory->create(
                        ['data' => ['shipment' => $shipment]]
                    );

                    $this->pdfRenderer->addDocument($document);
                }
            }
        }
    }
}
