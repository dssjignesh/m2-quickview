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

namespace Dss\Quickview\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;

/**
 * Product View block
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class View extends AbstractProduct implements IdentityInterface
{
    /**
     * @param Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param EncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface|PriceCurrencyInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        protected UrlEncoderInterface $urlEncoder,
        protected EncoderInterface $jsonEncoder,
        protected StringUtils $string,
        protected \Magento\Catalog\Helper\Product $productHelper,
        protected ConfigInterface $productTypeConfig,
        protected FormatInterface $localeFormat,
        protected Session $customerSession,
        protected ProductRepositoryInterface $productRepository,
        protected PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Return wishlist widget options
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWishlistOptions(): array
    {
        return ['productType' => $this->getProduct()->getTypeId()];
    }

    /**
     * Retrieve current product model
     *
     * @return Product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct(): Product
    {
        if (!$this->_coreRegistry->registry('product') && $this->getProductId()) {
            $product = $this->productRepository->getById($this->getProductId());
            $this->_coreRegistry->register('product', $product);
        }
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend(): bool
    {
        return false;
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = []): string
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')) {
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = ActionInterface::PARAM_NAME_URL_ENCODED;
        $addUrlValue = $this->_urlBuilder->getUrl('*/*/*', ['_use_rewrite' => true, '_current' => true]);
        $additional[$addUrlKey] = $this->urlEncoder->encode($addUrlValue);

        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    /**
     * Get JSON encoded configuration which can be used for JS dynamic price calculation depending on product options
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getJsonConfig(): string
    {
        /* @var $product Product */
        $product = $this->getProduct();

        if (!$this->hasOptions()) {
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $this->_localeFormat->getPriceFormat()
            ];
            return $this->jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $priceInfo = $product->getPriceInfo();
        $tierPricesList = $priceInfo->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = $tierPrice['price']->getValue() * 1;
        }
        $config = [
            'productId' => (int)$product->getId(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $priceInfo->getPrice('regular_price')->getAmount()->getValue() * 1,
                    'adjustments' => []
                ],
                'basePrice' => [
                    'amount' => $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount() * 1,
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount' => $priceInfo->getPrice('final_price')->getAmount()->getValue() * 1,
                    'adjustments' => []
                ]
            ],
            'idSuffix' => '_clone',
            'tierPrices' => $tierPrices
        ];

        $responseObject = new DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Return true if product has options
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function hasOptions(): bool
    {
        if ($this->getProduct()->getTypeInstance()->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function hasRequiredOptions(): bool
    {
        return $this->getProduct()->getTypeInstance()->hasRequiredOptions($this->getProduct());
    }

    /**
     * Define if setting of product options must be shown instantly.
     * Used in case when options are usually hidden and shown only when user
     * presses some button or link. In editing mode we better show these options
     * instantly.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isStartCustomization(): bool
    {
        return $this->getProduct()->getConfigureMode() || $this->_request->getParam('startcustomization');
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     *
     * Also restricts it by minimal qty.
     *
     * @param null|Product $product
     * @return int|float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductDefaultQty($product = null): int|float
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }

    /**
     * Get container name, where product options should be displayed
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptionsContainer(): string
    {
        return $this->getProduct()->getOptionsContainer() == 'container1' ? 'container1' : 'container2';
    }

    /**
     * Check whether quantity field should be rendered
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function shouldRenderQuantity(): bool
    {
        return !$this->productTypeConfig->isProductSet($this->getProduct()->getTypeId());
    }

    /**
     * Get Validation Rules for Quantity field
     *
     * @return array
     */
    public function getQuantityValidators(): array
    {
        $validators = [];
        $validators['required-number'] = true;
        return $validators;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIdentities(): array
    {
        $identities = $this->getProduct()->getIdentities();

        return $identities;
    }

    /**
     * Retrieve customer data object
     *
     * @return int
     */
    protected function getCustomerId(): int
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * Jone Encoder QuantityValidators
     *
     * @return string
     */
    public function jsonEncoderQuantityValidators(): string
    {
        return $this->jsonEncoder->encode($this->getQuantityValidators());
    }
}
