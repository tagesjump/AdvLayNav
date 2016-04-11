/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define(['jquery', 'advLayNavAjaxCall', 'jquery/ui'], function ($, advLayNavAjaxCall) {
    'use strict';

    $.widget('part.advlaynav', {
        _create: function() {
            this.element.find('a').each(function(index) {
                var link = $( this );
                if (link.attr('href') !== '#') {
                    link.attr('onclick', 'return false;');
                    var url = link.attr('href');
                    link.click(function () {
                        advLayNavAjaxCall(url);
                    });
                }
            });
        }
    });

    return $.part.advlaynav;
});
