<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfCore\Model\Config\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File;

class Customfont extends \Magento\Config\Model\Config\Backend\File
{
    const PATH_CUSTOMFONTS = 'downloadable/pdfcustomfonts/processed/';

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $file;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var
     */
    private $fileValue;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface $requestData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $uploaderFactory,
            $requestData,
            $filesystem,
            $resource,
            $resourceCollection,
            $data
        );
        $this->file = $file;
        $this->ioFile = $ioFile;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore -- Magento 2 Core use
    protected function _getAllowedExtensions()
    {
        return ['ttf'];
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $this->fileValue = $this->getValue();
        $this->setValue('');

        return $this;
    }

    public function afterSave()
    {
        if ($this->fileValue) {
            $file = $this->_getUploadDir() . DIRECTORY_SEPARATOR . $this->fileValue;
            $customFontPath = $this->_mediaDirectory->getAbsolutePath(self::PATH_CUSTOMFONTS);
            if (!$this->file->isExists($customFontPath)) {
                $this->file->createDirectory($customFontPath, DriverInterface::WRITEABLE_DIRECTORY_MODE);
            }
            $altFile = $this->_getUploadDir() . DIRECTORY_SEPARATOR
                . $this->ioFile->getPathInfo($this->fileValue)['filename'] . '_alternative_encoding.ttf';
            $this->file->copy($file, $altFile);
            $font = $this->addFontWithFallback($file, $altFile, $customFontPath);

            //Need to add faux bold option if it doesn't exist
            if ($font && substr($font, -1) !== 'b') {
                $fauxBoldFile = $this->_getUploadDir() . DIRECTORY_SEPARATOR
                    . $this->ioFile->getPathInfo($this->fileValue)['filename'] . '-bold.ttf';
                $fauxAltBoldFile = $this->_getUploadDir() . DIRECTORY_SEPARATOR
                    . $this->ioFile->getPathInfo($this->fileValue)['filename'] . '_alternative_encoding-bold.ttf';
                $this->file->copy($file, $fauxBoldFile);
                $this->file->copy($file, $fauxAltBoldFile);
                $this->addFontWithFallback($fauxBoldFile, $fauxAltBoldFile, $customFontPath);
            }
        }

        return parent::afterSave();
    }

    private function addFontWithFallback($file, $altFile, $path)
    {
        $fontName = \TCPDF_FONTS::addTTFfont($file, '', '', 32, $path);
        try {
            \TCPDF_FONTS::addTTFfont($altFile, '', '', 32, $path, 1, 0, true);
            return $fontName;
        } catch (\Exception $e) {
            return $fontName;
        }
    }
}
