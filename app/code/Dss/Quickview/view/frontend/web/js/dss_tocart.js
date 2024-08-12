/**
* Digit Software Solutions.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
*
* @category  Dss
* @package   Dss_Quickview
* @author    Extension Team
* @copyright Copyright (c) 2024 Digit Software Solutions. ( https://digitsoftsol.com )
*/
define(
    [
        'jquery',
        'Dss_Quickview/js/dss_tocart',
        'mage/mage',
        'Magento_Catalog/product/view/validation',
        'Magento_Catalog/js/catalog-add-to-cart'
    ],
    function ($) {
        'use strict';

        $.widget(
            'dss.dss_tocart',
            {
                _create: function () {
                    'use strict';
                    $('#product_addtocart_form').mage(
                        'validation',
                        {
                            radioCheckboxClosest: '.nested',
                            submitHandler: function (form) {
                                var widget = $(form).catalogAddToCart(
                                    {
                                        bindSubmit: false
                                    }
                                );
                                widget.catalogAddToCart('submitForm', $(form));
                                return false;
                            }
                        }
                    );
                    $('#ajax-goto a').click(
                        function (e) {
                            e.preventDefault();
                            window.top.location.href = $(this).attr('href');

                            return false;
                        }
                    );
                }
            }
        );
        return $.dss.dss_tocart;
    }
);
