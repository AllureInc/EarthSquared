<?php
namespace Earthsquared\Sortbyoptions\Plugin\Catalog\Model;

class Config
{
    const NEWEST_SORT_BY = 'newest';
    const LOW_TO_HIGH = 'low_to_high';
    const HIGH_TO_LOW = 'high_to_low';

    /**
     * Adding new sort option
     *
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param [] $result
     * @return []
     */
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $subject,
        $result
    ) {
        return array_merge(
            $result,
            [
                self::NEWEST_SORT_BY => __('Newest'),
                self::LOW_TO_HIGH => __('Price - Low To High'),
                self::HIGH_TO_LOW => __('Price - High To Low'),
            ]
        );
    }
}
