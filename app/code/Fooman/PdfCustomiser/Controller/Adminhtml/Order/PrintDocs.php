<?php
namespace Fooman\PdfCustomiser\Controller\Adminhtml\Order;

use Magento\Framework\Controller\ResultFactory;

/**
 * @author     Kristof Ringleff
 * @copyright  Copyright (c) 2009 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class PrintDocs extends Pdfdocs
{

    /**
     * @var \Fooman\PdfCore\Model\PdfFileHandling
     */
    private $pdfFileHandling;

    /**
     * @var \Fooman\PdfCore\Model\PdfRenderer
     */
    private $pdfRenderer;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Fooman\PdfCore\Model\PdfFileHandling $pdfFileHandling,
        \Fooman\PdfCore\Model\PdfRenderer $pdfRenderer,
        \Fooman\PdfCustomiser\Block\OrderFactory $orderDocumentFactory,
        \Fooman\PdfCustomiser\Block\InvoiceFactory $invoiceDocumentFactory,
        \Fooman\PdfCustomiser\Block\ShipmentFactory $shipmentDocumentFactory,
        \Fooman\PdfCustomiser\Block\CreditmemoFactory $creditmemoDocumentFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->collectionFactory = $orderCollectionFactory;
        $this->pdfFileHandling = $pdfFileHandling;
        $this->pdfRenderer = $pdfRenderer;

        parent::__construct(
            $context,
            $filter,
            $pdfFileHandling,
            $pdfRenderer,
            $orderDocumentFactory,
            $invoiceDocumentFactory,
            $shipmentDocumentFactory,
            $creditmemoDocumentFactory,
            $orderCollectionFactory
        );
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $orderIds = $this->_request->getParam('printIds');

        $collection = $this->collectionFactory->create()->addFieldToFilter('entity_id', ['in' => $orderIds]);
        $this->processCollection($collection);

        if ($this->pdfRenderer->hasPrintContent()) {
            return $this->pdfFileHandling->sendPdfFile($this->pdfRenderer);
        }

        $this->messageManager->addErrorMessage(__('Nothing to print'));
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('sales/order/index');
    }
}
