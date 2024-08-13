<?php

declare(strict_types=1);

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

namespace Dss\Quickview\Block\Product;

use Magento\Store\Model\ScopeInterface;

/**
 * Product View block
 */
class Social extends \Magento\Catalog\Block\Product\View
{
    public const XML_PATH_QUICKVIEW_REMOVE_PRODUCT_INFOR_MAILTO = 'dss_quickview/general/remove_product_info_mailto';

    /**
     * Get Config for show Email
     *
     * @return mixed
     */
    public function getConfigShowEmail(): mixed
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_QUICKVIEW_REMOVE_PRODUCT_INFOR_MAILTO,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }
}
