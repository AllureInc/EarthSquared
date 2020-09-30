<?php
namespace Dolphin\QuickOrder\Controller\Index;

class Add extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $coreSession;
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->coreSession = $coreSession;
        $this->_objectManager = $objectManager;
        $this->cart = $cart;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if (isset($_POST['rowcollection'])) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            $cookieData = json_decode($_POST['rowcollection'], true);
            if (count($cookieData) > 0) {
                $response = [
                    'errors' => false,
                    'productId' => '',
                    'message' => __("All Product's Added Successfully."),
                ];
                //print_r($cookieData);exit;
                foreach ($cookieData as $data) {
                    $product1 = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($data['product_id']);
                    $qty = $data['qty'];
                    if (count($data['selectedOptionsData']) > 0) {
                        $params1 = array(
                            'product' => $product1->getId(),
                            'super_attribute' => array(
                                '92' => $data['selectedOptionsData']['option_id'],
                            ),
                            'qty' => $qty,
                        );
                    } else {
                        $params1 = array(
                            'product' => $product1->getId(),
                            'qty' => $qty,
                        );
                    }
                    try {
                        $this->cart->addProduct($product1, $params1);
                    } catch (\Exception $e) {
                        $response = [
                            'errors' => true,
                            'productId' => $product1->getId(),
                            'message' => __($e->getMessage()),
                        ];
                        return $resultJson->setData($response);
                    }
                }
                try {
                    $this->cart->save();
                } catch (\Exception $e) {
                    $response = [
                        'errors' => true,
                        'productId' => '',
                        'message' => __($e->getMessage()),
                    ];
                }
            } else {
                $response = [
                    'errors' => true,
                    'productId' => '',
                    'message' => __('Please Select Product'),
                ];
            }
            return $resultJson->setData($response);
        }

        /*$items = $_REQUEST['rowcollection'];
    $this->coreSession->setQuickData($items);
    $sessionData = $this->coreSession->getQuickData();
    echo json_encode($sessionData);
    exit;*/
    }
}
