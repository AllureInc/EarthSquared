<?php
namespace Earthsquared\ProductSkuswitch\Plugin\ConfigurableProduct\Block\Product\View\Type;

class Configurable
{

    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        $result
    ) {

        $jsonResult = json_decode($result, true);
        $jsonResult['skus'] = [];
        $jsonResult['names'] = [];
        foreach ($subject->getAllowProducts() as $simpleProduct) {
            $jsonResult['skus'][$simpleProduct->getId()] = $simpleProduct->getSku();
            $jsonResult['names'][$simpleProduct->getId()] = $simpleProduct->getName();
        }
        $result = json_encode($jsonResult);
        return $result;
    }
}
