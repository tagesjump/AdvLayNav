/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define(['jquery', 'domReady', 'mage/apply/main', 'jquery/ui'], function ($, domReady, mage) {
    'use strict';

    $.widget('part.advlaynav', {
        _create: function() {
            this.element.find('a').each(function(index) {
                var link = $( this );
                if (link.attr('href') !== '#') {
                    link.attr('onclick', 'return false;');
                    link.click(function() {
                        var productListBlockName = $('#advlaynav_product_list_before').attr('data-block-name');
                        var navigationBlockName = $('#advlaynav_navigation_before').attr('data-block-name');
                        var blockNameParameters = '&productListBlockName=' + productListBlockName +
                            '&navigationBlockName=' + navigationBlockName;
                        var url = link.attr('href');
                        if (url.indexOf('?') > -1) {
                            url += '&advLayNavAjax=1'+blockNameParameters;
                        } else {
                            url += '?advLayNavAjax=1'+blockNameParameters;
                        }
                        $.ajax({
                            'url': url,
                            dataType: 'json'
                        }).done(function(data) {
                            history.pushState({}, '', link.attr('href'));
                            var productListContent = data[0];
                            var leftNavContent = data[1];

                            $('#advlaynav_product_list_before')
                                .nextUntil('#advlaynav_product_list_after')
                                .remove();
                            $(productListContent).insertAfter($('#advlaynav_product_list_before'));

                            $('#advlaynav_navigation_before')
                                .nextUntil('#advlaynav_navigation_after')
                                .remove();
                            $(leftNavContent).insertAfter($('#advlaynav_navigation_before'));

                            $(mage.apply);
                            $('#layered-filter-block').advlaynav();
                        });
                    });
                }
            });
        }
    });

    return $.part.advlaynav;
});
