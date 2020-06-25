<?php

namespace Dolphin\CustomerGroup\Observer\Frontend\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{

	private $customerRepository;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        CustomerRepositoryInterface $customerRepository
    ){
        $this->_request = $request;
        $this->customerRepository = $customerRepository;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
		$website = $this->_request->getParam('website_id_dws');
        if($website == 'trade'){
			$id = $observer->getEvent()->getCustomer()->getId();
			$customer = $this->customerRepository->getById($id);
			$group_id = $this->_request->getParam('group_id');
			$customer->setGroupId($group_id);
			$this->customerRepository->save($customer);
		}
    }
}

