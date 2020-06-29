<?php
namespace Earthsquared\ProductSkuswitch\Plugin\Swatches\Block\Product\Renderer;

class Configurable
{
    public function afterGetJsonConfig(\Magento\Swatches\Block\Product\Renderer\Configurable $subject, $result)
    {

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
