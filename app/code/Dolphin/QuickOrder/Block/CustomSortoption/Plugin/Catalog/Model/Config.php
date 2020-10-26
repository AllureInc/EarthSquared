<?php
namespace Dolphin\CustomSortoption\Plugin\Catalog\Model;

class Config
    {
        public function beforeGetAttributeUsedForSortByArray(
            \Magento\Catalog\Model\Config $catalogConfig,
            $options,
            $requestInfo = null
        ) {

            $options['old'] = __('Oldest');
            return $options;

        }

}