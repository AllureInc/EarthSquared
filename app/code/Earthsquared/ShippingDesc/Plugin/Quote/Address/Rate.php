<?php
namespace Earthsquared\ShippingDesc\Plugin\Quote\Address;

class Rate
{
    /**
     * @param \Magento\Quote\Model\Quote\Address\AbstractResult $rate
     * @return \Magento\Quote\Model\Quote\Address\Rate
     */
    public function afterImportShippingRate($subject, $result, $rate)
    {
        if ($rate instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {
            $result->setMethodDescription(
                $rate->getMethodDescription()
            );
        }

        return $result;
    }
}
