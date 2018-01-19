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
     * Disable for back ordered items
     */
    const PAYMENT_AFFIRM_DISABLE_BACK_ORDERED_ITEMS = 'payment/affirm/disable_for_backordered_items';

    /**
     * Checkout Flow Type
     */
    const PAYMENT_AFFIRM_CHECKOUT_FLOW_TYPE = 'payment/affirm/checkout_flow_type';

    /**
     * Disabled module
     *
     * @var bool
     */
    protected $_disabledModule;

    /**
     * Disabled back ordered on cart
     *
     * @var bool
     */
    protected $_disabledBackOrderedCart;

    /**
     * Disabled back ordered on PDP
     *
     * @var bool
     */
    protected $_disabledBackOrderedPdp;

    /**
     * Returns extension version
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        return (string)Mage::getConfig()->getNode()->modules->Affirm_Affirm->version;
    }

    /**
     * Returns is disable for back ordered items
     *
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isDisableForBackOrderedItems($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfigFlag(self::PAYMENT_AFFIRM_DISABLE_BACK_ORDERED_ITEMS, $store);
    }

    /**
     * Returns is enabled plain text
     *
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isPlainTextEnabled($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
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
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
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
    public function getApiUrl()
    {
        return Mage::getSingleton('affirm/credential')->getApiUrl();
    }

    /**
     * Get api key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiKey($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
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
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getSingleton('affirm/credential')->getSecretKey($store);
    }

    /**
     * Get detect xhr checkout
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getDetectXHRCheckout($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
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
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
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
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
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
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_PRE_ORDER, $store);
    }

    /**
     * Checkout flow type
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getCheckoutFlowType($store = null)
    {
        if($store == null) {
            $store = Mage::app()->getStore()->getStoreId();
        }
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_CHECKOUT_FLOW_TYPE, $store);
    }

    /**
     * Is Checkout flow type Modal
     *
     * @param Mage_Core_Model_Store $store
     * @return bool
     */
    public function isCheckoutFlowTypeModal($store = null)
    {
        $configCheckoutType = Mage::helper('affirm')->getCheckoutFlowType();
        if ($configCheckoutType == Affirm_Affirm_Model_Payment::CHECKOUT_FLOW_MODAL) {
            return true;
        } else {
            return false;
        }
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
     * Get affirm js text
     *
     * @return string
     */
    public function getAffirmJs()
    {
        $affirmJs = '<script type="text/javascript">
        if (!AFFIRM_AFFIRM.promos.getIsInitialized()) {
            AFFIRM_AFFIRM.promos.initialize("'.  $this->getApiKey() .'","'. $this->getAffirmJsUrl() .'");
        }
        if (!AFFIRM_AFFIRM.promos.getIsScriptLoaded()) {
            AFFIRM_AFFIRM.promos.loadScript();
        }
        </script>';
        return $affirmJs;
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

    /**
     * Skip promo messages for back ordered products PDP
     *
     * @return bool
     */
    public function isDisableProductBackOrdered()
    {
        if (null === $this->_disabledBackOrderedPdp) {
            $this->_disabledBackOrderedPdp = false;
            if (!Mage::helper('affirm')->isDisableForBackOrderedItems()) {
                $this->_disabledBackOrderedPdp = false;
                return $this->_disabledBackOrderedPdp;
            }
            $product = Mage::helper('catalog')->getProduct();
            if ($product && $product->getId()) {
                if ($product->isGrouped()) {
                    $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
                    foreach ($associatedProducts as $associatedProduct) {
                        $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($associatedProduct);
                        if ($inventory->getBackorders() && ($inventory->getQty() < 1)) {
                            $this->_disabledBackOrderedPdp = true;
                            break;
                        }
                    }
                } else {
                    $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
                    $this->_disabledBackOrderedPdp = $inventory->getBackorders() && ($inventory->getQty() < 1);
                }
                Mage::register('affirm_disabled_backordered', $this->_disabledBackOrderedPdp);
                return $this->_disabledBackOrderedPdp;
            }
        }
        return $this->_disabledBackOrderedPdp;
    }


    /**
     * Skip promo message for back ordered products cart
     *
     * @param null $quote
     * @return bool
     */
    public function isDisableQuoteBackOrdered($quote = null)
    {
        if (null === $this->_disabledBackOrderedCart) {
            if (!Mage::helper('affirm')->isDisableForBackOrderedItems()) {
                $this->_disabledBackOrderedCart = false;
                return $this->_disabledBackOrderedCart;
            }
            if (null === $quote) {
                $quote = Mage::helper('checkout/cart')->getQuote();
            }
            foreach ($quote->getAllItems() as $quoteItem) {
                $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($quoteItem->getProduct());
                if ($inventory->getBackorders() && (($inventory->getQty() - $quoteItem->getQty()) < 0)) {
                    $this->_disabledBackOrderedCart = true;
                    break;
                }
            }
            Mage::register('affirm_disabled_backordered', $this->_disabledBackOrderedCart);
        }
        return $this->_disabledBackOrderedCart;
    }

    /**
     * Get product on PDP
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return Mage::helper('catalog')->getProduct();
    }

    /**
     * Is product configurable
     *
     * @return bool
     */
    public function isProductConfigurable()
    {
        if ($this->getProduct() && $this->getProduct()->getId()) {
            return $this->getProduct()->isConfigurable() && $this->isDisableForBackOrderedItems();
        }
        return false;
    }

    /**
     * Get configurable back ordered info
     *
     * @return string
     */
    public function getConfigurableBackOrderedInfo()
    {
        $childProducts = Mage::getModel('catalog/product_type_configurable')
            ->getUsedProducts(null, $this->getProduct());
        $configurableAttributes = $this->getProduct()->getTypeInstance(true)
            ->getConfigurableAttributesAsArray($this->getProduct());
        $result = array();
        foreach ($childProducts as $childProduct) {
            foreach ($configurableAttributes as $configurableAttribute) {
                $result[$childProduct->getEntityId()][$configurableAttribute['attribute_id']] =
                    $childProduct[$configurableAttribute['attribute_code']];
            }
            $inventory = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct);
            $result[$childProduct->getEntityId()]['backorders'] = $inventory->getBackorders() &&
                ($inventory->getQty() < 1);
        }
        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * Get assets url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getAffirmAssetsUrl()
    {
        $prefix = "cdn-assets";
        $domain = "affirm.com";
        $assetPath = "images/banners";
        return 'https://' . $prefix . '.' . $domain . '/' . $assetPath ;
    }

    /**
     * Get template for button in order review page if Affirm method was selected and checkout flow type is modal
     *
     * @param string $name template name
     * @param string $block buttons block name
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && ($payment->getMethod() == Affirm_Affirm_Model_Payment::METHOD_CODE) && $this->isCheckoutFlowTypeModal()) {
                return $name;
            }
        }

        if ($blockObject = Mage::getSingleton('core/layout')->getBlock($block)) {
            return $blockObject->getTemplate();
        }

        return '';
    }

    /**
     * Get Affirm modal checkout js
     *
     * @return string
     */
    public function getAffirmCheckoutJsScript()
    {
        if (Mage::helper('affirm')->isCheckoutFlowTypeModal()) {
            return 'js/affirm/checkout.js';
        }
        return '';
    }

    /**
     * Returns a checkout object instance
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function _getCheckout()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

    /**
     * Get OPC save order URL
     *
     * @return string
     */
    public function getOPCCheckoutUrl()
    {
        $paramHttps  = (Mage::app()->getStore()->isCurrentlySecure()) ? array('_forced_secure' => true) : array();
        return Mage::getUrl('checkout/onepage/saveOrder/form_key/' . Mage::getSingleton('core/session')->getFormKey(), $paramHttps);
    }
}
