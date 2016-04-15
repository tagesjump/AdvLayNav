/**
 * Copyright © PART <info@part-online.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

define(["jquery", "advlaynav/ajaxcall", "jquery/ui"], function ($, ajaxCall) {
    "use strict";

    var createRangeSlider = function (minValue, maxValue, leftValue, rightValue, sliderId, amountId, valueUrl, removeUrl) {
        $("#" + sliderId).slider({
            range: true,
            min: minValue,
            max: maxValue,
            values: [ leftValue, rightValue ],
            slide: function (event, ui) {
                $("#" + amountId).text(ui.values[0] + " - " + ui.values[1]);
            },
            change: function( event, ui) {
                var ajaxUrl;
                if (ui.values[ 0 ] === minValue && ui.values[ 1 ] === maxValue) {
                    ajaxUrl = removeUrl;
                } else {
                    var replace;
                    if (ui.values[ 1 ] === maxValue) {
                        replace = ui.values[ 0 ] + "-";
                    } else {
                        replace = ui.values[ 0 ] + "-" + ui.values[ 1 ];
                    }
                    ajaxUrl = valueUrl.replace("option_id_placeholder", replace);
                }
                ajaxCall(ajaxUrl);
            }
        });
        $( "#" + amountId ).text( $( "#" + sliderId ).slider( "values", 0 ) +
          " - " + $( "#" + sliderId ).slider( "values", 1 ) );
    };

    return createRangeSlider;
});
