<?php
namespace Dolphin\DefaultAddress\Controller\Rewrite\Address;
class Delete extends \Magento\Customer\Controller\Address\Delete
{

    public function execute()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        $address = $this->_addressRepository->getById($addressId);
        if ($addressId && $this->_formKeyValidator->validate($this->getRequest())) {
            try {
                $address = $this->_addressRepository->getById($addressId);
                if ($address->getCustomerId() === $this->_getSession()->getCustomerId()) {
                    $this->_addressRepository->deleteById($addressId);
                    $this->messageManager->addSuccess(__('You deleted the address.'));
                    $customer_id =   $address->getCustomerId();
                    $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
                    $connection= $this->_resources->getConnection();

                    $customer_addressquote_table = $this->_resources->getTableName('quote_address');
                    $sql_customeraddressquote = "SELECT * FROM ".$customer_addressquote_table." WHERE customer_address_id = ".$addressId." ";
                    $connection->query($sql_customeraddressquote);
                    $result_customeraddress_id = $connection->fetchAll($sql_customeraddressquote);
                    if(!empty($result_customeraddress_id)){
                        $customer_address_table = $this->_resources->getTableName('quote_address');
                        $sql_customeraddress = "SELECT customer_address_id FROM ".$customer_address_table." WHERE customer_address_id = ".$addressId." ";

                        $connection->query($sql_customeraddress);
                        $result_customeraddress_id = $connection->fetchAll($sql_customeraddress);

                        foreach( $result_customeraddress_id as $result_customeraddress) {
                            foreach($result_customeraddress as $result_customeraddress_entity){
                                $themeTable = $this->_resources->getTableName('quote_address');
                                $sql = "Update " . $themeTable . " Set customer_address_id = null where customer_address_id = ".$result_customeraddress_entity."";
                                $connection->query($sql);
                           }

                        }
                    }
                } else {
                    $this->messageManager->addError(__('We can\'t delete the address right now.'));
                }
            } catch (\Exception $other) {
                $this->messageManager->addException($other, __('We can\'t delete the address right now.'));
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
