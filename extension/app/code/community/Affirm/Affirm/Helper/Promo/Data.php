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
 * Class Affirm_Affirm_Helper_Promo_Data
 */
class Affirm_Affirm_Helper_Promo_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config
     *
     * @var array
     */
    protected $_config;

    /**
     * Is promo active
     */
    const AFFIRM_PROMO_ACTIVE = 'affirmpromo/settings/active';

    /**
     * Is checkout button active
     */
    const PAYMENT_AFFIRM_CHECKOUT_BUTTON_ACTIVE = 'payment/affirm/checkout_button_active';

    /**
     * Checkout button image code
     */
    const PAYMENT_AFFIRM_CHECKOUT_BUTTON_CODE = 'payment/affirm/checkout_button_code';

    /**
     * Catalog product path
     */
    const AFFIRM_PROMO_CATALOG_PRODUCT_PATH = 'affirmpromo/developer_settings/path_catalog_product';

    /**
     * Catalog category path
     */
    const AFFIRM_PROMO_CATALOG_CATEGORY_PATH = 'affirmpromo/developer_settings/path_catalog_category';

    /**
     * Homepage path
     */
    const AFFIRM_PROMO_HOMEPAGE_PATH = 'affirmpromo/developer_settings/path_homepage';

    /**
     * Checkout cart path
     */
    const AFFIRM_PROMO_CHECKOUT_CART_PATH = 'affirmpromo/developer_settings/path_checkout_cart';

    /**
     * Affirm promo dev settings containers
     */
    const AFFIRM_PROMO_DEV_SETTINGS_CONTAINER = 'affirmpromo/developer_settings/container_';

    /**
     * Affirm pixel for checkout success/confirmation page
     */
    const AFFIRM_PROMO_PIXEL_CHECKOUT_SUCCESS = 'affirmpromo/pixel/add_checkout_success';

    /**
     * Affirm pixel for search query page
     */
    const AFFIRM_PROMO_PIXEL_SEARCH = 'affirmpromo/pixel/add_search';

    /**
     * Affirm pixel for product list page
     */
    const AFFIRM_PROMO_PIXEL_PRODUCT_LIST = 'affirmpromo/pixel/add_product_list';

    /**
     * Affirm pixel for product page
     */
    const AFFIRM_PROMO_PIXEL_PRODUCT_VIEW = 'affirmpromo/pixel/add_product_view';

    /**
     * Affirm pixel for add to cart action
     */
    const AFFIRM_PROMO_PIXEL_ADD_CART = 'affirmpromo/pixel/add_cart';

    /**
     * Affirm pixel for checkout start page
     */
    const AFFIRM_PROMO_PIXEL_CHECKOUT_START = 'affirmpromo/pixel/add_checkout_start';

    /**
     * PDP handle
     */
    const PDP_HANDLE = 'catalogproductview';

    /**
     * Returns is promo active
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isPromoActive($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return !Mage::helper('affirm')->isDisableModuleFunctionality() &&
            Mage::getStoreConfigFlag(self::AFFIRM_PROMO_ACTIVE, $store) &&
            !Mage::registry('affirm_disabled_backordered');
    }

    /**
     * Returns is checkout button active
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isCheckoutButtonActive($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfigFlag(self::PAYMENT_AFFIRM_CHECKOUT_BUTTON_ACTIVE, $store);
    }

    /**
     * Get checkout button code
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCheckoutButtonCode($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_CHECKOUT_BUTTON_CODE, $store);
    }

    /**
     * Get catalog product path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCatalogProductPath($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::AFFIRM_PROMO_CATALOG_PRODUCT_PATH, $store);
    }

    /**
     * Get catalog category path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCatalogCategoryPath($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::AFFIRM_PROMO_CATALOG_CATEGORY_PATH, $store);
    }

    /**
     * Get homepage path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getHomepagePath($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::AFFIRM_PROMO_HOMEPAGE_PATH, $store);
    }

    /**
     * Get checkout cart path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCheckoutCartPath($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::AFFIRM_PROMO_CHECKOUT_CART_PATH, $store);
    }

    /**
     * Get container settings
     *
     * @param null|Mage_Core_Model_Store $store
     * @param string $pageCode
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getContainerSettings($store = null, $pageCode)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::AFFIRM_PROMO_DEV_SETTINGS_CONTAINER . $pageCode, $store);
    }

    /**
     * Get configuration settings for current page
     *
     * @return Varien_Object
     */
    public function getSectionConfig()
    {
        if (null === $this->_config) {
            $codeMap = array(
                $this->getCatalogProductPath() => 'catalog_product',
                $this->getCatalogCategoryPath() => 'catalog_category',
                $this->getHomepagePath() => 'homepage',
                $this->getCheckoutCartPath() => 'checkout_cart'
            );
            $config = new Varien_Object();
            $module = Mage::app()->getRequest()->getModuleName();
            $controller = Mage::app()->getRequest()->getControllerName();
            $action = Mage::app()->getRequest()->getActionName();

            if (isset($codeMap[$module . '.' . $controller . '.' . $action])) {
                $pageCode = $codeMap[$module . '.' . $controller . '.' . $action];
                $size = Mage::getStoreConfig('affirmpromo/' . $pageCode . '/size');
                $position = Mage::getStoreConfig('affirmpromo/' . $pageCode . '/position');
                list($positionHorizontal, $positionVertical) = explode('-', $position);
                $display = Mage::getStoreConfig('affirmpromo/' . $pageCode . '/display');
                $config->setPageCode($pageCode)
                    ->setDisplay($display)
                    ->setSize($size)
                    ->setPositionHorizontal($positionHorizontal)
                    ->setPositionVertical($positionVertical);

                // each fetch container for a given page
                $config->setContainer($this->getContainerSettings(null, $pageCode));
            }
            $this->_config = $config;
        }

        return $this->_config;
    }

    /**
     * Get checkout promo Affirm js
     *
     * @return string
     */
    public function getCheckoutAffirmJsScript()
    {
        if (!Mage::helper('affirm/promo_asLowAs')->isAsLowAsDisabledOnCheckout()) {
            return 'js/affirm/aslowas.js';
        }
        return 'js/affirm/noconf.js';
    }

    /**
     * Get pdp promo Affirm js
     *
     * @return string
     */
    public function getPDPAffirmJsScript()
    {
        if (!Mage::helper('affirm/promo_asLowAs')->isAsLowAsDisabledOnPDP()) {
            return 'js/affirm/aslowas.js';
        }
        return 'js/affirm/noconf.js';
    }

    /**
     * Get plp promo Affirm js
     *
     * @return string
     */
    public function getPLPAffirmJsScript()
    {
        if (!Mage::helper('affirm/promo_asLowAs')->isAsLowAsDisabledOnPLP()) {
            return 'js/affirm/aslowas.js';
        }
        return 'js/affirm/noconf.js';
    }

    /**
     * Returns is pixel placement for confirmation page enabled
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isCheckoutSuccessPixelEnabled($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfigFlag(self::AFFIRM_PROMO_PIXEL_CHECKOUT_SUCCESS, $store);
    }

    /**
     * Returns is pixel placement for search query enabled
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isSearchTrackPixelEnabled($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfigFlag(self::AFFIRM_PROMO_PIXEL_SEARCH, $store);
    }

    /**
     * Returns is pixel placement for product list page enabled
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isProductListTrackPixelEnabled($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfigFlag(self::AFFIRM_PROMO_PIXEL_PRODUCT_LIST, $store);
    }

    /**
     * Returns is pixel placement for product page enabled
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isProductViewTrackPixelEnabled($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfigFlag(self::AFFIRM_PROMO_PIXEL_PRODUCT_VIEW, $store);
    }

    /**
     * Returns is pixel placement for add to cart action enabled
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isAddCartTrackPixelEnabled($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfigFlag(self::AFFIRM_PROMO_PIXEL_ADD_CART, $store);
    }

    /**
     * Returns is pixel placement for checkout start action enabled
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isAddChkStartTrackPixelEnabled($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfigFlag(self::AFFIRM_PROMO_PIXEL_CHECKOUT_START, $store);
    }

    /**
     * get Date with Microtime.
     *
     * @return string
     */
    public function getDateMicrotime()
    {
        $microtime = explode(' ', microtime());
        $msec = $microtime[0];
        $msecArray = explode('.', $msec);
        $date = date('Y-m-d-H-i-s') . '-' . $msecArray[1];
        return $date;
    }
}