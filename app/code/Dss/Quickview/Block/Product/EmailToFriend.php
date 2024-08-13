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

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product;

class EmailToFriend extends Template
{
    /**
     * @param ProductHelper $productHelper
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        protected ProductHelper $productHelper,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get Email to Friend URL for the product
     *
     * @param Product $product
     * @return string
     */
    public function getEmailToFriendUrl(Product $product): string
    {
        return $this->productHelper->getEmailToFriendUrl($product);
    }
}
