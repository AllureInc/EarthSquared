/*
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//spectrum is bundled with Magento 2.3.0+
//use the bundled version and fallback to the PdfCore one when it's not available
var config = {
    paths: {
        'spectrum': [
            'jquery/spectrum/spectrum',
            'Fooman_PdfCore/js/spectrum'
        ]
    },
    map: {
        '*': {
            foomanPdfCoreColourPicker: 'Fooman_PdfCore/js/pick-colour'
        }
    }
};
