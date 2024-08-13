define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        var btnTextColor = config.btnTextColor;
        var rgba = config.rgba;

        $(document).ready(function() {
            var style = `
                a.dss-quickview {
                    color: ${btnTextColor};
                    background-color: rgba(${rgba}, 0.6);
                }
            `;

            $('<style>').prop('type', 'text/css').html(style).appendTo('head');
        });
    };
});
