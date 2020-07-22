<?php

namespace Dolphin\ConfigurableProduct\Rewrite\Magento\ConfigurableProduct\Block\Product\View\Type;

class Configurable
{

    /**
     * Get product images for configurable variations
     *
     * @return array
     * @since 100.1.10
     */
    protected function aroundGetOptionImages()
    {
        echo "hello over";exit;
        $images = [];

        foreach ($this->getAllowProducts() as $product) {
            $productImages = $this->helper->getGalleryImages($product) ?: [];
            $thumb = array();
            foreach ($productImages as $image) {

                if (!in_array($image->getData('small_image_url'), $thumb)) {
                    $thumb[] = $image->getData('small_image_url');
                } else {
                    continue;
                }
                $images[$product->getId()][] =
                    [
                    'thumb' => $image->getData('small_image_url'),
                    'img' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url'),
                    'caption' => $image->getLabel(),
                    'position' => $image->getPosition(),
                    'isMain' => $image->getFile() == $product->getImage(),
                    'type' => str_replace('external-', '', $image->getMediaType()),
                    'videoUrl' => $image->getVideoUrl(),
                ];
            }
        }

        return $images;
    }
}
