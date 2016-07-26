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
 * Class Affirm_Affirm_Helper_Data
 */
class Affirm_Affirm_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Path to title plain text enabler
     */
    const AFFIRM_PLAIN_TEXT_ENABLED = 'payment/affirm/plain_text_title_enabled';

    /**
     * Path to label html
     */
    const LABEL_HTML_CUSTOM = 'payment/affirm/label_html_custom';

    /**
     * Payment affirm XHR checkout
     */
    const PAYMENT_AFFIRM_XHR_CHECKOUT = 'payment/affirm/detect_xhr_checkout';

    /**
     * Min order threshold
     */
    const PAYMENT_AFFIRM_MIN_ORDER_TOTAL = 'payment/affirm/min_order_total';

    /**
     * Max order threshold
     */
    const PAYMENT_AFFIRM_MAX_ORDER_TOTAL = 'payment/affirm/max_order_total';

    /**
     * Pre order
     */
    const PAYMENT_AFFIRM_PRE_ORDER = 'payment/affirm/pre_order';

    /**
     * Disabled module
     *
     * @var bool
     */
    protected $_disabledModule;

    /**
     * Returns is enabled plain text
     *
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isPlainTextEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::AFFIRM_PLAIN_TEXT_ENABLED, $store);
    }

    /**
     * Check is base currency non dollar
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return bool
     */
    public function isNonDollarCurrencyStore($method)
    {
        return !in_array(Mage::app()->getStore()->getBaseCurrencyCode(), $method->getAcceptedCurrencyCodes());
    }

    /**
     * Is disable module functionality
     *
     * @return string
     */
    public function isDisableModuleFunctionality()
    {
        if (null === $this->_disabledModule) {
            $payments = Mage::getSingleton('payment/config')->getAllMethods();
            $method = $payments[Affirm_Affirm_Model_Payment::METHOD_CODE];
            $this->_disabledModule = $this->isNonDollarCurrencyStore($method);
        }
        return $this->_disabledModule;
    }

    /**
     * Returns html of label
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getLabelHtmlAfter($store = null)
    {
        return Mage::getStoreConfig(self::LABEL_HTML_CUSTOM, $store);
    }

    /**
     * Get module version
     *
     * @return string
     */
    public function getModuleConfigVersion()
    {
        return Mage::getConfig()->getModuleConfig('Affirm_Affirm')->version;
    }

    /**
     * Get api url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiUrl($store = null)
    {
        return Mage::getSingleton('affirm/credential')->getApiUrl($store);
    }

    /**
     * Get api key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiKey($store = null)
    {
        return Mage::getSingleton('affirm/credential')->getApiKey($store);
    }

    /**
     * Get secret key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getSecretKey($store = null)
    {
        return Mage::getSingleton('affirm/credential')->getSecretKey($store);
    }

    /**
     * Get financial product key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getFinancialProductKey($store = null)
    {
        return Mage::getSingleton('affirm/credential')->getFinancialProductKey($store);
    }

    /**
     * Get detect xhr checkout
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getDetectXHRCheckout($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_XHR_CHECKOUT, $store);
    }

    /**
     * Get min order total threshold
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMinOrderThreshold($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_MIN_ORDER_TOTAL, $store);
    }

    /**
     * Get max order total threshold
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMaxOrderThreshold($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_MAX_ORDER_TOTAL, $store);
    }

    /**
     * Check is pre order
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function isPreOrder($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_PRE_ORDER, $store);
    }

    /**
     * Get affirm js url
     *
     * @return string
     */
    public function getAffirmJsUrl()
    {
        $apiUrl = $this->getApiUrl();
        $parsedUrl = Mage::getModel('core/url')->parseUrl($apiUrl);
        $domain = $parsedUrl->getHost();
        $domain = str_ireplace('www.', '', $domain);
        $domain = str_ireplace('api.', '', $domain);
        $prefix = 'cdn1.';
        if (strpos($domain, 'sandbox') === 0) {
            $prefix = 'cdn1-';
        }
        return 'https://' . $prefix . '' . $domain . '/js/v2/affirm.js';
    }

    /**
     * Is xhr request
     *
     * @param array $proxyRequest
     * @return bool
     */
    public function isXhrRequest($proxyRequest)
    {
        $detectedXhr = isset($proxyRequest['xhr']) && $proxyRequest['xhr'];
        $configXhr = Mage::helper('affirm')->getDetectXHRCheckout();
        if ($configXhr == Affirm_Affirm_Model_Payment::CHECKOUT_REDIRECT) {
            return false;
        } elseif ($configXhr == Affirm_Affirm_Model_Payment::CHECKOUT_XHR) {
            return true;
        } else {
            return $detectedXhr;
        }
    }

    /**
     * Get affirm checkout token
     *
     * @return string
     */
    public function getAffirmTokenCode()
    {
        return Mage::registry('affirm_token_code');
    }

    /**
     * Check is affirm payment method is available
     *
     * @return bool
     */
    public function isAffirmPaymentMethodAvailable()
    {
        $isAvailable = false;
        $method = $this->getAffirmPaymentMethod();
        if ($method) {
            $isAvailable = $method->isAvailable(Mage::helper('checkout/cart')->getQuote());
        }
        return $isAvailable;
    }

    /**
     * Check is affirm payment method is enabled
     *
     * @return bool
     */
    public function isAffirmPaymentMethodEnabled()
    {
        $method = $this->getAffirmPaymentMethod();
        $isEnabled = $method ? $method->canUseCheckout() : false;
        return $isEnabled;
    }

    /**
     * Get affirm payment method
     *
     * @return Affirm_Affirm_Model_Payment}null
     */
    public function getAffirmPaymentMethod()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $method = isset($payments[Affirm_Affirm_Model_Payment::METHOD_CODE])
                ? $payments[Affirm_Affirm_Model_Payment::METHOD_CODE]
                : null;
        return $method;
    }

    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}
