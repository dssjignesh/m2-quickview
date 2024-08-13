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
        "jquery",
    ],
    function ($) {
        $(".product-addto-links .towishlist").hover(
            function (e) {
               var dataPost=$(this).attr("data-post");
                var urlWishList="wishlist\\/index\\/add";
                var urlDssWistList="dss_quickview\\/wishlist\\/add";
                dataPost=dataPost.replace(urlWishList,urlDssWistList);
                urlWishList="wishlist/index/add";
                urlDssWistList="dss_quickview/wishlist/add";
                dataPost=dataPost.replace(urlWishList,urlDssWistList);
                $(this).attr("data-post",dataPost);
            }
        );
    }
);
