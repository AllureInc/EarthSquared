<?php
/**
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\PdfDesign\Model;

/**
 * Design source for Alternative Pdf Design Two
 */
class AlternativeDesignTwo extends AlternativeDesign
{

    public function getLayoutHandle($pdfType)
    {
        return sprintf('fooman_pdfcustomiser_alt_2_%s', $pdfType);
    }
}
