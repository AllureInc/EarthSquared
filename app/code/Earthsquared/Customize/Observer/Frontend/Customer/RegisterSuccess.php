<?php 
namespace Earthsquared\Customize\Observer\Frontend\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface 
{
    /**
     * @var CustomerRepositoryInterface
     */

    private $customerRepository;
    protected $_storeManager; 
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
         CustomerRepositoryInterface $customerRepository,
         \Magento\Store\Model\StoreManagerInterface $storeManager
        )
    {
        $this->_request = $request;
        $this->_storeManager = $storeManager; 
        $this->customerRepository = $customerRepository;
    }    
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ){      

        $id = $observer->getEvent()->getCustomer()->getId();
        $customer = $this->customerRepository->getById($id);

        //$group_id = $this->_request->getParam('group_id');
        if($this->_storeManager->getStore()->getWebsiteId() == 1){
            $customer->setGroupId(2);
            $this->customerRepository->save($customer);
        }   

    }
}