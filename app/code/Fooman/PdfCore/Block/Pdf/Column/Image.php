<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCore\Block\Pdf\Column;

use Fooman\PdfCore\Helper\FileOps;

class Image extends \Fooman\PdfCore\Block\Pdf\Column implements \Fooman\PdfCore\Block\Pdf\ColumnInterface
{
    const DEFAULT_WIDTH = 18;
    const COLUMN_TYPE = 'fooman_image';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var FileOps
     */
    protected $file;

    /**
     * @var \Fooman\PdfCore\Helper\ParamKey
     */
    protected $paramKeyHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context         $context
     * @param \Magento\Catalog\Model\ResourceModel\Product    $productResource
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param FileOps                                         $file
     * @param \Fooman\PdfCore\Helper\ParamKey                 $paramKeyHelper
     * @param array                                           $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        FileOps $file,
        \Fooman\PdfCore\Helper\ParamKey $paramKeyHelper,
        array $data = []
    ) {
        $this->productResource = $productResource;
        $this->productRepository = $productRepository;
        $this->paramKeyHelper = $paramKeyHelper;
        $this->file = $file;
        parent::__construct($context, $data);
    }

    public function getGetter()
    {
        return [$this, 'getImage'];
    }

    /**
     * @param $row
     *
     * @return string
     */
    public function getImage($row)
    {
        $dim = $this->getImageDimensions();
        $imagePath = $this->getImagePath($row);
        if ($imagePath) {
            $params = [
                $imagePath,
                null,
                null,
                null,
                $dim['image'],
                null,
                null,
                null,
                true
            ];
            return sprintf(
                '<p align="%s"><tcpdf method="Image" %s /></p><span style="line-height:%s;"></span>',
                $this->getAlignment() ? $this->getAlignment() : 'left',
                $this->paramKeyHelper->getEncodedParams($params),
                $dim['spacer']
            );
        }

        return '';
    }

    private function getImageDimensions($size = 'default')
    {
        $sizes = [
            'large'      => ['image' => '20', 'spacer' => '21mm'],
            'xtra-large' => ['image' => '30', 'spacer' => '31mm'],
            'default'    => ['image' => '15', 'spacer' => '21mm'],
            'small'      => ['image' => '12', 'spacer' => '13mm']
        ];
        if (isset($sizes[$size])) {
            return $sizes[$size];
        }
        return $sizes['default'];
    }

    /**
     * @param $row
     *
     * @return bool|string
     */
    public function getImagePath($row)
    {
        $imagePath = false;
        $attribute = $this->productResource->getAttribute('image');
        $orderItem = $this->getOrderItem($row);
        if ($orderItem->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
            && $orderItem->getProductOptionByCode('simple_sku')) {
            try {
                $product = $this->productRepository->get(
                    $orderItem->getProductOptionByCode('simple_sku'),
                    false,
                    $row->getStoreId()
                );
                $imagePath = $attribute->getFrontend()->getValue($product);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $imagePath = false;
            }
        }

        //fallback on main configurable if no image is found on simple product above
        if ((!$imagePath) || ($imagePath === 'no_selection')) {
            try {
                $product = $this->productRepository->getById($orderItem->getProductId(), false, $row->getStoreId());
                $imagePath = $attribute->getFrontend()->getValue($product);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $imagePath = false;
            }
        }

        //We might still have a chance of picking up an image from a grouped parent product
        if ((!$imagePath) || ($imagePath === 'no_selection')) {
            $productOptions = $orderItem->getProductOptions();
            if (isset($productOptions['super_product_config']['product_id'])) {
                $productId = $productOptions['super_product_config']['product_id'];
                try {
                    $product = $this->productRepository->getById($productId, false, $row->getStoreId());
                    $imagePath = $attribute->getFrontend()->getValue($product);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $imagePath = false;
                }
            }
        }

        if ($imagePath) {
            $fullPath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath('/catalog/product/' . ltrim($imagePath, '/'));
            if ($this->file->fileExists($fullPath)) {
                return $fullPath;
            }
        }
        return false;
    }
}
