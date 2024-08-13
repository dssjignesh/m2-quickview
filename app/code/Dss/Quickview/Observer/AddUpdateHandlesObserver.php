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

namespace Dss\Quickview\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Layout;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;

class AddUpdateHandlesObserver implements ObserverInterface
{
    public const XML_PATH_QUICKVIEW_REMOVE_TAB = 'dss_quickview/general/remove_product_tab';
    public const XML_PATH_QUICKVIEW_REMOVE_ADDTO_COMPARE = 'dss_quickview/general/remove_addto_compare';
    public const XML_PATH_QUICKVIEW_REMOVE_ADDTO_WISHLIST = 'dss_quickview/general/remove_addto_wishlist';
    public const XML_PATH_QUICKVIEW_REMOVE_REVIEWS = 'dss_quickview/general/remove_reviews';
    public const XML_PATH_QUICKVIEW_REMOVE_PRODUCT_RELATED = 'dss_quickview/general/remove_product_related';
    public const XML_PATH_QUICKVIEW_REMOVE_PRODUCT_UPSELL = 'dss_quickview/general/remove_product_upsell';
    public const XML_PATH_QUICKVIEW_REMOVE_PRODUCT_INFOR_MAILTO = 'dss_quickview/general/remove_product_info_mailto';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Http $request
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        protected ScopeConfigInterface $scopeConfig,
        protected Http $request,
        protected StoreManagerInterface $storeManager,
        protected ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Add New Layout handle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getData('layout');
        $fullActionName = $observer->getData('full_action_name');

        if ($fullActionName != 'dss_quickview_catalog_product_view') {
            return $this;
        }

        $productId = $this->request->getParam('id');
        if (isset($productId)) {
            try {
                $product = $this->productRepository->getById(
                    $productId,
                    false,
                    $this->storeManager->getStore()->getId()
                );
            } catch (NoSuchEntityException $e) {
                return false;
            }

            $productType = $product->getTypeId();

            $layout->getUpdate()->addHandle('dss_quickview_catalog_product_view_type_' . $productType);
        }
        $this->quickViewRemove($layout);
        return $this;
    }

    /**
     * Quick view remove
     *
     * @param Layout $layout
     */
    protected function quickViewRemove($layout)
    {
        $removeTab = $this->scopeConfig->getValue(
            self::XML_PATH_QUICKVIEW_REMOVE_TAB,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($removeTab == 0) {
            $layout->getUpdate()->addHandle('dss_quickview_removeproduct_tab');
        }
        $removeAddToCompare = $this->scopeConfig->getValue(
            self::XML_PATH_QUICKVIEW_REMOVE_ADDTO_COMPARE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($removeAddToCompare == 0) {
            $layout->getUpdate()->addHandle('dss_quickview_remove_addtocompare');
        }
        $removeAddToWishList = $this->scopeConfig->getValue(
            self::XML_PATH_QUICKVIEW_REMOVE_ADDTO_WISHLIST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($removeAddToWishList == 0) {
            $layout->getUpdate()->addHandle('dss_quickview_remove_addtowishlist');
        }
        $removeReviews = $this->scopeConfig->getValue(
            self::XML_PATH_QUICKVIEW_REMOVE_REVIEWS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($removeReviews == 0) {
            $layout->getUpdate()->addHandle('dss_quickview_remove_reviews');
        }
        $removeProductRelated = $this->scopeConfig->getValue(
            self::XML_PATH_QUICKVIEW_REMOVE_PRODUCT_RELATED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($removeProductRelated == 0) {
            $layout->getUpdate()->addHandle('dss_quickview_remove_product_related');
        }
        $removeProductUpsell = $this->scopeConfig->getValue(
            self::XML_PATH_QUICKVIEW_REMOVE_PRODUCT_UPSELL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($removeProductUpsell == 0) {
            $layout->getUpdate()->addHandle('dss_quickview_remove_product_upsell');
        }
        $layout->getUpdate()->addHandle('dss_quickview_product_info_mailto');
    }
}
