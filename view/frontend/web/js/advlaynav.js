/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define(['jquery', 'domReady', 'jquery/ui',], function ($, domReady) {
    'use strict';

    $.widget('part.advlaynav', {
        _create: function() {
            this.element.find('a').each(function(index) {
                var link = $( this );
                if (link.attr('href') !== '#') {
                    link.attr('onclick', 'return false;');
                    link.click(function() {
                        $.ajax({
                            url: link.attr('href') + "$isLayerAjax=1"
                        }).done(function(data) {
                            history.pushState({}, '', link.attr('href'));
                            console.log(data);
                        });
                    });
                }
            });
        }
    });

    return $.part.advlaynav;
});
