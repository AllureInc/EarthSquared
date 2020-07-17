/*
 * @copyright Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define([
    'jquery',
    'spectrum'
], function (jQuery, colorpicker) {
    'use strict';

    return function (config, element) {
        jQuery(element).spectrum({
            showInitial: true,
            preferredFormat: "hex",
            showInput: true
        });
    };

});