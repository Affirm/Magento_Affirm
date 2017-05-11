<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category    Affirm
 * @package     Affirm_Affirm
 * @copyright   Copyright (c) 2014 One Pica, Inc. (http://www.onepica.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Promo key
     */
    const AFFIRM_PROMO_KEY = 'affirmpromo/settings/promo_key';

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
     * PDP handle
     */
    const PDP_HANDLE = 'catalogproductview';

    /**
     * Returns is promo active
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isPromoActive($store = null)
    {
        return !Mage::helper('affirm')->isDisableModuleFunctionality() &&
            Mage::getStoreConfigFlag(self::AFFIRM_PROMO_ACTIVE, $store) &&
            !Mage::registry('affirm_disabled_backordered');
    }

    /**
     * Get promo key
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getPromoKey($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_KEY, $store);
    }

    /**
     * Returns is checkout button active
     *
     * @param null|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isCheckoutButtonActive($store = null)
    {
        return Mage::getStoreConfigFlag(self::PAYMENT_AFFIRM_CHECKOUT_BUTTON_ACTIVE, $store);
    }

    /**
     * Get checkout button code
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCheckoutButtonCode($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_CHECKOUT_BUTTON_CODE, $store);
    }

    /**
     * Get catalog product path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCatalogProductPath($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_CATALOG_PRODUCT_PATH, $store);
    }

    /**
     * Get catalog category path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCatalogCategoryPath($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_CATALOG_CATEGORY_PATH, $store);
    }

    /**
     * Get homepage path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getHomepagePath($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_HOMEPAGE_PATH, $store);
    }

    /**
     * Get checkout cart path
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getCheckoutCartPath($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_CHECKOUT_CART_PATH, $store);
    }

    /**
     * Get container settings
     *
     * @param null|Mage_Core_Model_Store $store
     * @param string $pageCode
     * @return string
     */
    public function getContainerSettings($store = null, $pageCode)
    {
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
}