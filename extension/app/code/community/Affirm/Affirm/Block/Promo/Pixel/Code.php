<?php
/**
 *
 *  * BSD 3-Clause License
 *  *
 *  * Copyright (c) 2018, Affirm
 *  * All rights reserved.
 *  *
 *  * Redistribution and use in source and binary forms, with or without
 *  * modification, are permitted provided that the following conditions are met:
 *  *
 *  *  Redistributions of source code must retain the above copyright notice, this
 *  *   list of conditions and the following disclaimer.
 *  *
 *  *  Redistributions in binary form must reproduce the above copyright notice,
 *  *   this list of conditions and the following disclaimer in the documentation
 *  *   and/or other materials provided with the distribution.
 *  *
 *  *  Neither the name of the copyright holder nor the names of its
 *  *   contributors may be used to endorse or promote products derived from
 *  *   this software without specific prior written permission.
 *  *
 *  * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 *  * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 *  * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 *  * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 *  * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 *  * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 *  * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 *  * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 *  * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 *  * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

/**
 * Affirm tracking pixel
 */

class Affirm_Affirm_Block_Promo_Pixel_Code extends Mage_Core_Block_Template
{
    /**
     * Tax display flag
     *
     * @var null|int
     */
    public $taxDisplayFlag = null;

    /**
     * Tax catalog flag
     *
     * @var null|int
     */
    public $taxCatalogFlag = null;

    /**
     * Store object
     *
     * @var null|Mage_Core_Model_Store
     */
    public $store = null;

    /**
     * Store ID
     *
     * @var null|int
     */
    public $storeId = null;

    /**
     * Base currency code
     *
     * @var null|string
     */
    public $baseCurrencyCode = null;

    /**
     * Current currency code
     *
     * @var null|string
     */
    public $currentCurrencyCode = null;

    /**
     * Returns product data needed for tracking.
     *
     * @return array
     */
    public function getProductData()
    {
        $p = Mage::registry('current_product');

        $data = array();
        if($p) {
            $data['name'] = $this->escapeSingleQuotes($p->getName());
            $data['productId'] = $this->escapeSingleQuotes($p->getSku());
            $data['price'] = Mage::helper('affirm/util')->formatCents(
                $this->getProductPrice($p)
            );
            $data['currency'] = $this->getCurrentCurrencyCode();
        }

        return $data;
    }

    /**
     * Returns category data needed for tracking.
     *
     * @return array
     */
    public function getCategoryData()
    {
        $_category = Mage::registry('current_category');
        $data = array();
        if ($_category) {
            $data['categoryId'] = $_category->getId();
            $data['categoryName'] = $_category->getName();
        }
        return $data;
    }

    /**
     * Returns last added product to cart data needed for tracking.
     *
     * @return array
     */
    public function getAddToCartData()
    {
        $productID = Mage::getSingleton('checkout/session')->getLastAddedProductId(true);
        $data = array();
        if ($productID) {
            $_product = Mage::getModel('catalog/product')->load($productID);
            $data['productId'] = $this->escapeSingleQuotes($_product->getSku());
            $data['name'] = $this->escapeSingleQuotes($_product->getName());
            $data['price'] = Mage::helper('affirm/util')->formatCents(
                $this->getProductPrice($_product)
            );
            $data['currency'] = $this->getCurrentCurrencyCode();
        }
        return $data;
    }

    /**
     * Returns cart data needed for tracking.
     *
     * @return array
     */
    public function getCartData()
    {
        $data = array();
        $session= Mage::getSingleton('checkout/session');
        $items = $session->getQuote()->getAllVisibleItems();
        if($items) {
            foreach ($items as $item) {
                $productData = array();
                $productData['productId'] = $this->escapeSingleQuotes($item->getSku());
                $productData['name'] = $this->escapeSingleQuotes($item->getName());
                $productData['price'] = Mage::helper('affirm/util')->formatCents(
                    $item->getPrice()
                );
                $productData['currency'] = $this->getCurrentCurrencyCode();
                $productData['quantity'] = $item->getQty();
                $data[] = $productData;
            }
        }
        return $data;
    }

    /**
     * Returns quote data needed for checkout started tracking.
     *
     * @return array|null
     */
    public function getQuoteData()
    {
        $quote= Mage::getSingleton('checkout/session')->getQuote();
        $data = array();
        if ($quote) {
            $data['checkoutId'] = $quote->getId();
            $data['currency'] = $quote->getQuoteCurrencyCode();
            $data['total'] = Mage::helper('affirm/util')->formatCents($quote->getGrandTotal());
        }
        return $data;

    }

    /**
     * Returns quote products data needed for checkout started tracking.
     *
     * @return array|null
     */
    public function getQuoteProductsData()
    {
        $quote= Mage::getSingleton('checkout/session')->getQuote();
        $data = array();
        if ($quote) {
             foreach ($quote->getAllVisibleItems() as $item) {
                $productData = array();
                $productData['productId'] = $this->escapeSingleQuotes($item->getSku());
                $productData['name'] = $this->escapeSingleQuotes($item->getName());
                $productData['price'] = Mage::helper('affirm/util')->formatCents(
                    $item->getPrice()
                );
                $productData['currency'] = $this->getCurrentCurrencyCode();
                $productData['quantity'] = $item->getQty();
                $data[] = $productData;
            }
        }
        return $data;

    }

    /**
     * Add slashes to string and prepares string for javascript.
     *
     * @param string $str
     * @return string
     */
    public function escapeSingleQuotes($str)
    {
        return str_replace("'", "\'", $str);
    }

    /**
     * Returns store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->store === null) {
            $this->store = Mage::app()->getStore();
        }

        return $this->store;
    }

    /**
     * Returns Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->storeId = $this->getStore()->getId();
        }

        return $this->storeId;
    }

    /**
     * Returns base currency code
     * (3 letter currency code like USD, GBP, EUR, etc.)
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        if ($this->baseCurrencyCode === null) {
            $this->baseCurrencyCode = strtoupper(
                $this->getStore()->getBaseCurrencyCode()
            );
        }

        return $this->baseCurrencyCode;
    }

    /**
     * Returns current currency code
     * (3 letter currency code like USD, GBP, EUR, etc.)
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        if ($this->currentCurrencyCode === null) {
            $this->currentCurrencyCode = strtoupper(
                $this->getStore()->getCurrentCurrencyCode()
            );
        }

        return $this->currentCurrencyCode;
    }

    /**
     * Returns flag based on "System > Cofiguration > Sales > Tax
     * > Price Display Settings > Display Product Prices In Catalog"
     * Returns 0 or 1 instead of 1, 2, 3.
     *
     * @return int
     */
    public function getDisplayTaxFlag()
    {
        if ($this->taxDisplayFlag === null) {
            // Tax Display
            // 1 - excluding tax
            // 2 - including tax
            // 3 - including and excluding tax
            $flag = (int) Mage::getStoreConfig(
                'tax/display/type',
                $this->getStoreId()
            );

            // 0 means price excluding tax, 1 means price including tax
            if ($flag == 1) {
                $this->taxDisplayFlag = 0;
            } else {
                $this->taxDisplayFlag = 1;
            }
        }

        return $this->taxDisplayFlag;
    }

    /**
     * Returns System > Cofiguration > Sales > Tax > Calculation Settings
     * > Catalog Prices configuration value
     *
     * @return int
     */
    public function getCatalogTaxFlag()
    {
        // Are catalog product prices with tax included or excluded?
        if ($this->taxCatalogFlag === null) {
            $this->taxCatalogFlag = (int) Mage::getStoreConfig(
                    'tax/calculation/price_includes_tax',
                    $this->getStoreId()
                );
        }

        // 0 means excluded, 1 means included
        return $this->taxCatalogFlag;
    }

    /**
     * Returns product price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductPrice($product)
    {
        $price = 0.0;

        switch ($product->getTypeId()) {
            case 'bundle':
                $price =  $this->getBundleProductPrice($product);
                break;
            case 'configurable':
                $price = $this->getConfigurableProductPrice($product);
                break;
            case 'grouped':
                $price = $this->getGroupedProductPrice($product);
                break;
            default:
                $price = $this->getFinalPrice($product);
        }

        return $price;
    }

    /**
     * Returns bundle product price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getBundleProductPrice($product)
    {
        $includeTax = (bool) $this->getDisplayTaxFlag();

        return $this->getFinalPrice(
            $product,
            $product->getPriceModel()->getTotalPrices(
                $product,
                'min',
                $includeTax,
                1
            )
        );
    }

    /**
     * Returns configurable product price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getConfigurableProductPrice($product)
    {
        if ($product->getFinalPrice() === 0) {
            $configurable = Mage::getModel('catalog/product_type_configurable')
                ->setProduct($product);

            $simpleCollection = $configurable->getUsedProductCollection()
                ->addAttributeToSelect('price')->addFilterByRequiredOptions();

            foreach ($simpleCollection as $simpleProduct) {
                if ($simpleProduct->getPrice() > 0) {
                    return $this->getFinalPrice($simpleProduct);
                }
            }
        }

        return $this->getFinalPrice($product);
    }

    /**
     * Returns grouped product price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getGroupedProductPrice($product)
    {
        $assocProducts = $product->getTypeInstance(true)
            ->getAssociatedProductCollection($product)
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('tax_class_id')
            ->addAttributeToSelect('tax_percent');

        $minPrice = INF;
        foreach ($assocProducts as $assocProduct) {
            $minPrice = min($minPrice, $this->getFinalPrice($assocProduct));
        }

        return $minPrice;
    }

    /**
     * Returns final price.
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $price
     * @return string
     */
    public function getFinalPrice($product, $price = null)
    {
        if ($price === null) {
            $price = $product->getFinalPrice();
        }

        if ($price === null) {
            $price = $product->getData('special_price');
        }

        $productType = $product->getTypeId();

        // 1. Convert to current currency if needed

        // Convert price if base and current currency are not the same
        if ($this->getBaseCurrencyCode() !== $this->getCurrentCurrencyCode()) {
            // Convert to from base currency to current currency
            $price = $this->getStore()->getBaseCurrency()
                ->convert($price, $this->getCurrentCurrencyCode());
        }

        // 2. Apply tax if needed

        // Simple, Virtual, Downloadable products price is without tax
        // Grouped products have associated products without tax
        // Bundle products price already have tax included/excluded
        // Configurable products price already have tax included/excluded
        if ($productType != 'bundle') {
            // If display tax flag is on and catalog tax flag is off
            if ($this->getDisplayTaxFlag() && !$this->getCatalogTaxFlag()) {
                $price = Mage::helper('tax')->getPrice(
                    $product,
                    $price,
                    true,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    false,
                    false
                );
            }

            // Case when catalog prices are with tax but display tax is set to
            // to exclude tax. Applies for all products except for bundle
            // If display tax flag is off and catalog tax flag is on
            if (!$this->getDisplayTaxFlag() && $this->getCatalogTaxFlag()) {
                $price = Mage::helper('tax')->getPrice(
                    $product,
                    $price,
                    false,
                    null,
                    null,
                    null,
                    $this->getStoreId(),
                    true,
                    false
                );
            }
        }

        return $price;
    }
}
