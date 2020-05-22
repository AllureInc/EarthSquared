<?php
namespace Earthsquared\Sortbyoptions\Plugin\Catalog\Block;

class Toolbar
{
    const NEWEST_SORT_BY = 'newest';
    const LOW_TO_HIGH = 'low_to_high';
    const HIGH_TO_LOW = 'high_to_low';
    /**
     * Around Plugin
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Data\Collection $collection
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $subject,
        \Closure $proceed,
        $collection
    ) {
        $currentOrder = $subject->getCurrentOrder();
        $currentDirection = $subject->getCurrentDirection();
        $result = $proceed($collection);

        if ($currentOrder == self::NEWEST_SORT_BY) {
            if ($currentDirection == 'desc') {
                $subject->getCollection()->setOrder('created_at', 'desc');
            } else {
                $subject->getCollection()->setOrder('created_at', 'asc');
            }
        }
        if ($currentOrder == 'high_to_low') {
            $subject->getCollection()->setOrder('price', 'desc');
        } elseif ($currentOrder == 'low_to_high') {
            $subject->getCollection()->setOrder('price', 'asc');
        }
        return $result;
    }
}
