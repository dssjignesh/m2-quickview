<?php

declare(strict_types= 1);

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

namespace Dss\Quickview\Plugin\Catalog\Controller\Product;

use Magento\Framework\App\Action\Context;
use Dss\Quickview\Helper\Data;

class View
{
    /**
     * View construct.
     *
     * @param Context $context
     * @param Data $quickViewDss
     */
    public function __construct(
        protected Context $context,
        protected Data $quickViewDss
    ) {
    }

    /**
     * Get url quick view.
     *
     * @param \Dss\Simpledetailconfigurable\Plugin\Catalog\Controller\Product\View $subject
     * @param string $result
     * @param \Magento\Catalog\Model\Product|mixed $product
     * @param string $paramRedirect
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetUrlRedirect($subject, $result, $product, $paramRedirect)
    {
        $params = $this->context->getRequest();
        if ($params->getControllerModule() === 'Dss_Quickview') {
            return $this->quickViewDss->getUrl() . 'id/' . $product->getId() . $paramRedirect;
        }
        return $result;
    }
}
