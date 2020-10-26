<?php
namespace Dolphin\CustomSortoption\Plugin\Catalog\Block;
    
class Toolbar
    {

        public function aroundSetCollection(
            \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
            \Closure $proceed,
            $collection
        ) {
            $currentOrder = $subject->getCurrentOrder();
            $result = $proceed($collection);

            if ($currentOrder) {
                if ($currentOrder == 'old') {
                    $subject->getCollection()->setOrder('created_at', 'asc');
                } 
            }else{
                $subject->getCollection()->getSelect()->reset('order');
                $subject->getCollection()->setOrder('created_at', 'asc');
            }
            return $result;
        }

}