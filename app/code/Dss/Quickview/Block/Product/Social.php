<?php

declare(strict_types= 1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
     */
    public function getConfigShowEmail()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_QUICKVIEW_REMOVE_PRODUCT_INFOR_MAILTO,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }
}
