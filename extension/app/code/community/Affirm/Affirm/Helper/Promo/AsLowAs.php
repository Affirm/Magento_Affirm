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
 * Class Affirm_Affirm_Helper_Promo_AsLowAs
 */
class Affirm_Affirm_Helper_Promo_AsLowAs extends Mage_Core_Helper_Abstract
{
    /**
     * List of skipped product types
     *
     * @var array
     */
    protected $_skippedTypes = array(Mage_Catalog_Model_Product_Type::TYPE_GROUPED);

    /**
     * Disabled back ordered on cart
     *
     * @var bool
     */
    protected $_affirmDisabledBackOrderedCart;

    /**
     * Disabled back ordered on PDP
     *
     * @var bool
     */
    protected $_affirmDisabledBackOrderedPdp;

    /**
     * Visibility as low as on PDP
     */
    const AFFIRM_PROMO_AS_LOW_AS_PDP = 'affirmpromo/as_low_as/as_low_as_pdp';

    /**
     * Visibility as low as on cart
     */
    const AFFIRM_PROMO_AS_LOW_AS_CART = 'affirmpromo/as_low_as/as_low_as_cart';

    /**
     * As low as APR value
     */
    const AFFIRM_PROMO_AS_LOW_AS_APR_VALUE = 'affirmpromo/as_low_as/apr_value';

    /**
     * As low as months options
     */
    const AFFIRM_PROMO_AS_LOW_AS_PROMO_MONTHS = 'affirmpromo/as_low_as/promo_months';

    /**
     * Check is visible on PDP
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function isVisibleOnPDP($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_AS_LOW_AS_PDP, $store);
    }

    /**
     * Check is visible on cart
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function isVisibleOnCart($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_AS_LOW_AS_CART, $store);
    }

    /**
     * Get APR value
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getAPRValue($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_AS_LOW_AS_APR_VALUE, $store);
    }

    /**
     * Get promo months options
     *
     * @param null|Mage_Core_Model_Store $store
     * @return string
     */
    public function getPromoMonths($store = null)
    {
        return Mage::getStoreConfig(self::AFFIRM_PROMO_AS_LOW_AS_PROMO_MONTHS, $store);
    }

    /**
     * Skip As Low As message for specific product types
     *
     * @return bool
     */
    protected function _isSkipProductByType()
    {
        $product = Mage::helper('catalog')->getProduct();
        if ((null === $product) || !$product->getId()) {
            return true;
        }
        return in_array($product->getTypeId(), $this->_skippedTypes);
    }

    /**
     * Is As Low As disabled on checkout
     *
     * @return bool
     */
    public function isAsLowAsDisabledOnCheckout()
    {
        if (null === $this->_affirmDisabledBackOrderedCart) {
            $this->_affirmDisabledBackOrderedCart = Mage::helper('affirm')->isDisableQuoteBackOrdered() ||
                Mage::helper('affirm')->isDisableModuleFunctionality() ||
                !$this->isVisibleOnCart() || !Mage::helper('affirm')->isAffirmPaymentMethodEnabled();
        }
        return $this->_affirmDisabledBackOrderedCart;
    }

    /**
     * Is As Low As disabled on PDP
     *
     * @return bool
     */
    public function isAsLowAsDisabledOnPDP()
    {
        if (null === $this->_affirmDisabledBackOrderedPdp) {
            $this->_affirmDisabledBackOrderedPdp = Mage::helper('affirm')->isDisableProductBackOrdered() ||
                Mage::helper('affirm')->isDisableModuleFunctionality() ||
                !$this->isVisibleOnPDP() || !Mage::helper('affirm')->isAffirmPaymentMethodEnabled() ||
                $this->_isSkipProductByType();
        }
        return $this->_affirmDisabledBackOrderedPdp;
    }
}
