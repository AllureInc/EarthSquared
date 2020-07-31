<?php
namespace Dolphin\QuickOrder\Controller\Index;

class Add extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $coreSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory) {
        $this->_pageFactory = $pageFactory;
        $this->coreSession = $coreSession;

        return parent::__construct($context);
    }

    public function execute()
    {   
        $items = $_REQUEST['rowcollection'];                        
        $this->coreSession->setQuickData($items);        
        $sessionData =  $this->coreSession->getQuickData();    
        echo json_encode($sessionData);        
        exit;
    }
}
