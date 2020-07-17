<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfDesign\Model\Api;

interface DesignInterface
{
    public function getLayoutHandle($pdfType);

    public function getItemStyling();

    public function getTemplateFiles();

    public function getFooterLayoutHandle();

    public function getStoreId();

    public function setStoreId($storeId);
}
