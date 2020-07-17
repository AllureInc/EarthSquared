<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfDesign\Block\DesignOne;

use Fooman\PdfCore\Helper\ParamKey;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Module\Dir;

/**
 * Block class for adaptive icons
 */
class Icons extends \Fooman\PdfCore\Block\Pdf\Block
{

    const PRIMARY_COLOUR_PLACEHOLDER = '***FOOMAN_PRIMARY_COLOUR***';
    const SECONDARY_COLOUR_PLACEHOLDER = '***FOOMAN_SECONDARY_COLOUR***';

    /**
     * @var ParamKey
     */
    private $paramKeyHelper;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $moduleReader;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $file;

    /**
     * @param Context  $context
     * @param ParamKey $paramKeyHelper
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param array    $data
     */
    public function __construct(
        Context $context,
        ParamKey $paramKeyHelper,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Io\File $file,
        array $data = []
    ) {
        $this->paramKeyHelper = $paramKeyHelper;
        $this->moduleReader = $moduleReader;
        $this->file = $file;
        parent::__construct($context, $data);
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function getEncodedParams(array $params)
    {
        return $this->paramKeyHelper->getEncodedParams($params);
    }

    public function getCity()
    {
        return $this->_scopeConfig->getValue(
            'general/store_information/city',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    public function getStreetAddress()
    {
        $streets = [];
        $streetOne = $this->_scopeConfig->getValue(
            'general/store_information/street_line1',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        $streetTwo = $this->_scopeConfig->getValue(
            'general/store_information/street_line2',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        if ($streetOne) {
            $streets[] = $streetOne;
        }
        if ($streetTwo) {
            $streets[] = $streetTwo;
        }

        return implode(', ', $streets);
    }

    public function getPostcode()
    {
        return $this->_scopeConfig->getValue(
            'general/store_information/postcode',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    public function getPhone()
    {
        return $this->_scopeConfig->getValue(
            'general/store_information/phone',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    public function getEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_custom1/email',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    public function getWebsite()
    {
        $url = $this->_scopeConfig->getValue(
            'web/unsecure/base_url',
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
        $urlProtFree = rtrim(str_replace(['https://', 'http://'], '', $url), '/');
        return sprintf('<a style="color:black; text-decoration: none;" href="%s">%s</a>', $url, $urlProtFree);
    }

    public function getSvgIconAsString($iconName, $primColour, $secColour)
    {
        $fileContents = $this->file->read(
            $this->moduleReader->getModuleDir(Dir::MODULE_VIEW_DIR, 'Fooman_PdfDesign')
            . '/frontend/web/images/svg/pdf-design-1/' . $iconName . '.svg'
        );

        $processedFileContents = str_replace(
            [self::PRIMARY_COLOUR_PLACEHOLDER, self::SECONDARY_COLOUR_PLACEHOLDER],
            [$primColour, $secColour],
            $fileContents
        );

        return '@' . $processedFileContents;
    }
}
