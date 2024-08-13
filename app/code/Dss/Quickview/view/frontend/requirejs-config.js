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

var config = {
    map: {
        '*': {
            dss_config: 'Dss_Quickview/js/dss_config',
            magnificPopup: 'Dss_Quickview/js/jquery.magnific-popup.min',
            dss_tocart: 'Dss_Quickview/js/dss_tocart'
        }
    },
    shim: {
        magnificPopup: {
            deps: ['jquery']
        }
    },
    config : {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Dss_Quickview/js/add-to-cart-mixin': true
            }
        }
    }
};
