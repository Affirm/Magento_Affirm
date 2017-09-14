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
 * Class Affirm_Affirm_Model_Payment
 */
class Affirm_Affirm_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    /**#@+
     * Define constants
     */
    const API_CHARGES_PATH = '/api/v2/charges/';
    const API_CHECKOUT_PATH = '/api/v2/checkout/';
    const CHECKOUT_XHR_AUTO = 'auto';
    const CHECKOUT_XHR = 'xhr';
    const CHECKOUT_REDIRECT = 'redirect';
    /**#@-*/

    /**
     * Form block type
     */
    protected $_formBlockType = 'affirm/payment_form';

    /**
     * Info block type
     */
    protected $_infoBlockType = 'affirm/payment_info';

    /**#@+
     * Define constants
     */
    const CHECKOUT_TOKEN = 'checkout_token';
    const METHOD_CODE = 'affirm';
    /**#@-*/

    /**
     * Code
     *
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**#@+
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;
    protected $_canFetchTransactionInfo = true;
    protected $_allowCurrencyCode       = array('USD');
    /**#@-*/

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode)
    {
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }

    /**
     * Is needed initialize
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        if ($this->getCheckoutToken()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Return array of currency codes supplied by Payment Gateway
     *
     * @return array
     */
    public function getAcceptedCurrencyCodes()
    {
        if (!$this->hasData('_accepted_currency')) {
            $acceptedCurrencyCodes = $this->_allowCurrencyCode;
            $acceptedCurrencyCodes[] = $this->getConfigData('currency');
            $this->setData('_accepted_currency', $acceptedCurrencyCodes);
        }
        return $this->_getData('_accepted_currency');
    }

    /**
     * Get charge ID
     *
     * @return string
     */
    public function getChargeId()
    {
        return $this->getInfoInstance()->getAdditionalInformation('charge_id');
    }

    /**
     * Get checkout token
     *
     * @return string
     */
    public function getCheckoutToken()
    {
        return $this->getInfoInstance()->getAdditionalInformation(self::CHECKOUT_TOKEN);
    }

    /**
     * Set charge Id
     *
     * @param string $chargeId
     * @return Mage_Payment_Model_Info
     */
    public function setChargeId($chargeId)
    {
        return $this->getInfoInstance()->setAdditionalInformation('charge_id', $chargeId);
    }

    /**
     * Get base api url
     *
     * @return string
     */
    public function getBaseApiUrl()
    {
        return Mage::helper('affirm')->getApiUrl();
    }

    /**
     * Api request
     *
     * @param  mixed  $method
     * @param  string $path
     * @param  null|array $data
     * @param  string $resourcePath
     * @return string
     * @throws Affirm_Affirm_Exception
     */
    protected function _apiRequest($method, $path, $data = null, $resourcePath = self::API_CHARGES_PATH)
    {
        $url = trim($this->getBaseApiUrl(), '/') . $resourcePath . $path;
        Mage::log($url);

        $client = new Zend_Http_Client($url);

        if ($method == Zend_Http_Client::POST && $data) {
            $json = json_encode($data);
            $client->setRawData($json, 'application/json');
        }

        $client->setAuth(Mage::helper('affirm')->getApiKey(),
            Mage::helper('affirm')->getSecretKey(), Zend_Http_Client::AUTH_BASIC
        );

        $rawResult = $client->request($method)->getRawBody();
        try {
            $retJson = Zend_Json::decode($rawResult, Zend_Json::TYPE_ARRAY);
        } catch (Zend_Json_Exception $e) {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Invalid affirm response: '. $rawResult));
        }

        //validate to make sure there are no errors here
        if (isset($retJson['status_code'])) {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Affirm error code:'.
                    $retJson['status_code'] . ' error: '. $retJson['message']));
        }
        return $retJson;
    }

    /**
     * Get checkout from tocken
     *
     * @param string $token
     * @return string
     */
    protected function _getCheckoutFromToken($token)
    {
        return $this->_apiRequest(Zend_Http_Client::GET, $token, null, self::API_CHECKOUT_PATH);
    }

    /**
     * Get checkout total
     *
     * @param string $token
     * @return string
     */
    protected function _getCheckoutTotalFromToken($token)
    {
        $res = $this->_getCheckoutFromToken($token);
        return $res['total'];
    }

    /**
     * Set charge result
     *
     * @param array $result
     * @throws Affirm_Affirm_Exception
     */
    protected function _setChargeResult($result)
    {
        if (isset($result['id'])) {
            $this->setChargeId($result['id']);
        } else {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Affirm charge id not returned from call.'));
        }
    }

    /**
     * Validate
     *
     * @param string $amount
     * @param string $affirmAmount
     * @throws Affirm_Affirm_Exception
     */
    protected function _validateAmountResult($amount, $affirmAmount)
    {
        if ($affirmAmount != $amount) {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__(
                'Your cart amount has changed since starting your Affirm application. Please try again.'
                )
            );
        }
    }

    /**
     * Send capture request to gateway
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Affirm_Affirm_Exception
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Invalid amount for capture.'));
        }
        $chargeId = $this->getChargeId();
        $amountCents = Mage::helper('affirm/util')->formatCents($amount);
        if (!$chargeId) {
            if ($this->getCheckoutToken()) {
                $this->authorize($payment, $amount);
                $chargeId = $this->getChargeId();
            } else {
                throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Charge id have not been set.'));
            }
        }
        $result = $this->_apiRequest(Varien_Http_Client::POST, "{$chargeId}/capture");
        $this->_validateAmountResult($amountCents, $result['amount']);
        $payment->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Identify is refund partial (compatibility issue for earlier CE version)
     *
     * @param Varien_Object $payment
     * @return $this
     */
    protected function _identifyPartialRefund(Varien_Object $payment)
    {
        $canRefundMore = $payment->getOrder()->canCreditmemo();
        $payment->setShouldCloseParentTransaction(!$canRefundMore);
        return $this;
    }

    /**
     * Refund capture
     *
     * @param Varien_Object $payment
     * @param float         $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Affirm_Affirm_Exception
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Invalid amount for refund.'));
        }
        $chargeId = $this->getChargeId();
        $amountCents = Mage::helper('affirm/util')->formatCents($amount);
        if (!$chargeId) {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Charge id have not been set.'));
        }
        $result = $this->_apiRequest(Varien_Http_Client::POST, "{$chargeId}/refund", array(
                'amount' => $amountCents)
        );

        $this->_validateAmountResult($amountCents, $result['amount']);
        $type = Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND;
        $id = Mage::getModel('core/date')->date('His');
        if (!$id) {
            $id = 1;
        }
        $payment->setTransactionId("{$this->getChargeId()}-{$id}-{$type}")->setIsTransactionClosed(1);
        $this->_identifyPartialRefund($payment);
        return $this;
    }

    /**
     * Void
     *
     * @param Varien_Object $payment
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Affirm_Affirm_Exception
     */
    public function void(Varien_Object $payment)
    {
        if (!$this->canVoid($payment)) {
            throw new Affirm_Affirm_Exception(Mage::helper('payment')->__('Void action is not available.'));
        }
        $chargeId = $this->getChargeId();
        if (!$chargeId) {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Charge id have not been set.'));
        }
        $result = $this->_apiRequest(Varien_Http_Client::POST, "{$chargeId}/void");
        return $this;
    }

    /**
     * Cancel (apply void if applicable)
     *
     * @param Varien_Object $payment
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Affirm_Affirm_Exception
     */
    public function cancel(Varien_Object $payment)
    {
        if ($payment->canVoid($payment)) {
            $this->void($payment);
        };
        return parent::cancel($payment);
    }

    /**
     * Send authorize request to gateway
     *
     * @param  Varien_Object $payment
     * @param  float $amount
     * @return $this|Mage_Payment_Model_Abstract
     * @throws Affirm_Affirm_Exception
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            throw new Affirm_Affirm_Exception(Mage::helper('affirm')->__('Invalid amount for authorization.'));
        }

        $amountCents = Mage::helper('affirm/util')->formatCents($amount);
        $token = $payment->getAdditionalInformation(self::CHECKOUT_TOKEN);
        $amountToAuthorize = $this->_getCheckoutTotalFromToken($token);
        $this->_validateAmountResult($amountCents, $amountToAuthorize);

        $result = $this->_apiRequest(Varien_Http_Client::POST, '', array(
                self::CHECKOUT_TOKEN => $token)
        );

        $this->_setChargeResult($result);
        $payment->setTransactionId($this->getChargeId())->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Varien_Object $stateObject
     * @return Mage_Payment_Model_Abstract|void
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

    /**
     * Set affirm checkout token
     *
     *@param string $checkoutToken
     */
    public function setAffirmCheckoutToken($checkoutToken)
    {
        $payment = $this->getInfoInstance();
        $payment->setAdditionalInformation(self::CHECKOUT_TOKEN, $checkoutToken);
    }

    /**
     * Process confirmation order (after return from affirm, pre_order=0)
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $checkoutToken
     */
    public function processConfirmOrder($order, $checkoutToken)
    {
        $payment = $order->getPayment();

        $payment->setAdditionalInformation(self::CHECKOUT_TOKEN, $checkoutToken);
        $action = $this->getConfigData('payment_action');

        //authorize the total amount.
        $payment->authorize(true, self::_affirmTotal($order));
        $payment->setAmountAuthorized(self::_affirmTotal($order));
        $order->save();
        //can capture as well..
        if ($action == self::ACTION_AUTHORIZE_CAPTURE) {
            $payment->setAmountAuthorized(self::_affirmTotal($order));
            $payment->setBaseAmountAuthorized($order->getBaseTotalDue());
            $payment->capture(null);
            $order->save();
        }
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        if (!$this->redirectPreOrder()) {
            return Mage::getUrl('affirm/payment/redirect', array('_secure' => true));
        }
    }

    /**
     * Get checkout object
     *
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getCheckoutObject($order)
    {
        $shippingAddress = $order->getShippingAddress();
        $shipping = null;
        if ($shippingAddress) {
            $shipping = array(
                'name' => array('full' => $shippingAddress->getName()),
                'phone_number' => $shippingAddress->getTelephone(),
                'phone_number_alternative' => $shippingAddress->getAltTelephone(),
                'address' => array(
                    'line1'   => $shippingAddress->getStreet(1),
                    'line2'   => $shippingAddress->getStreet(2),
                    'city'    => $shippingAddress->getCity(),
                    'state'   => $shippingAddress->getRegion(),
                    'country' => $shippingAddress->getCountryModel()->getIso2Code(),
                    'zipcode' => $shippingAddress->getPostcode(),
                ));
        }

        $billingAddress = $order->getBillingAddress();
        $billing = array(
            'name' => array('full' => $billingAddress->getName()),
            'email' => $order->getCustomerEmail(),
            'phone_number' => $billingAddress->getTelephone(),
            'phone_number_alternative' => $billingAddress->getAltTelephone(),
            'address' => array(
                'line1'   => $billingAddress->getStreet(1),
                'line2'   => $billingAddress->getStreet(2),
                'city'    => $billingAddress->getCity(),
                'state'   => $billingAddress->getRegion(),
                'country' => $billingAddress->getCountryModel()->getIso2Code(),
                'zipcode' => $billingAddress->getPostcode(),
            ));

        $items = array();
        $productIds = array();
        $productItemsMFP = array();
        $categoryItemsIds = array();
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $productIds[] = $orderItem->getProductId();
        }
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(
                array('affirm_product_mfp', 'affirm_product_mfp_type', 'affirm_product_mfp_priority')
            )
            ->addAttributeToFilter('entity_id', array('in' => $productIds));
        $productItems = $products->getItems();
        foreach ($order->getAllVisibleItems() as $orderItem) {
            $product = $productItems[$orderItem->getProductId()];
            if (Mage::helper('affirm')->isPreOrder() && $orderItem->getParentItem() &&
                ($orderItem->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ) {
                continue;
            }
            $items[] = array(
                'sku' => $orderItem->getSku(),
                'display_name' => $orderItem->getName(),
                'item_url' => $product->getProductUrl(),
                'item_image_url' => $product->getImageUrl(),
                'qty' => intval($orderItem->getQtyOrdered()),
                'unit_price' => Mage::helper('affirm/util')->formatCents($orderItem->getPrice())
            );

            $start_date = $product->getAffirmProductMfpStartDate();
            $end_date = $product->getAffirmProductMfpEndDate();
            if(empty($start_date) || empty($end_date)) {
                $mfpValue = $product->getAffirmProductMfp();
            } else {
                if(Mage::app()->getLocale()->isStoreDateInInterval(null, $start_date, $end_date)) {
                    $mfpValue = $product->getAffirmProductMfp();
                } else {
                    $mfpValue = "";
                }
            }

            $productItemsMFP[] = array(
                'value' => $mfpValue,
                'type' => $product->getAffirmProductMfpType(),
                'priority' => $product->getAffirmProductMfpPriority() ?
                    $product->getAffirmProductMfpPriority() : 0
            );

            $categoryIds = $product->getCategoryIds();
            if (!empty($categoryIds)) {
                $categoryItemsIds = array_merge($categoryItemsIds, $categoryIds);
            }
        }

        $checkout = array(
            'checkout_id' => $order->getIncrementId(),
            'currency' => $order->getOrderCurrencyCode(),
            'shipping_amount' => Mage::helper('affirm/util')->formatCents($order->getShippingAmount()),
            'shipping_type' => $order->getShippingMethod(),
            'tax_amount' => Mage::helper('affirm/util')->formatCents($order->getTaxAmount()),
            'merchant' => array(
                'public_api_key' => Mage::helper('affirm')->getApiKey(),
                'user_confirmation_url' => Mage::getUrl('affirm/payment/confirm', array('_secure' => true)),
                'user_cancel_url' => Mage::helper('checkout/url')->getCheckoutUrl(),
                'user_confirmation_url_action' => 'POST',
                'charge_declined_url' => Mage::helper('checkout/url')->getCheckoutUrl()
            ),
            'config' => array('required_billing_fields' => 'name,address,email'),
            'items' => $items,
            'billing' => $billing
        );

        // By convention, Affirm expects positive value for discount amount. Magento provides negative.
        $discountAmtAffirm = (-1) * $order->getDiscountAmount();
        if ($discountAmtAffirm > 0.001) {
            $discountCode = $this->_getDiscountCode($order);
            $checkout['discounts'] = array(
                $discountCode => array(
                    'discount_amount' => Mage::helper('affirm/util')->formatCents($discountAmtAffirm)
                )
            );
        }

        if ($shipping) {
            $checkout['shipping'] = $shipping;
        }
        $checkout['total'] = Mage::helper('affirm/util')->formatCents(self::_affirmTotal($order));
        if (method_exists('Mage', 'getEdition')){
            $platform_edition = Mage::getEdition();
        }
        $platform_version = Mage::getVersion();
        $platform_version_edition = isset($platform_edition) ? $platform_version.' '.$platform_edition : $platform_version;
        $checkout['metadata'] = array(
            'shipping_type' => $order->getShippingDescription(),
            'platform_type' => 'Magento',
            'platform_version' => $platform_version_edition,
            'platform_affirm' => Mage::helper('affirm')->getExtensionVersion()
        );
        $affirmMFPValue = Mage::helper('affirm/mfp')->getAffirmMFPValue($productItemsMFP, $categoryItemsIds, $order->getBaseGrandTotal());
        if ($affirmMFPValue) {
            $checkout['financing_program'] = $affirmMFPValue;
        }

        $checkoutObject = new Varien_Object($checkout);
        Mage::dispatchEvent('affirm_get_checkout_object_after', array('checkout_object' => $checkoutObject));
        $checkout = $checkoutObject->getData();

        return $checkout;
    }

    /**
     * Get discount code
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getDiscountCode($order)
    {
        return $order->getDiscountDescription();
    }

    /**
     * Affirm total
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected static function _affirmTotal($order)
    {
        return $order->getTotalDue();
    }

    /**
     * Redirect pre-order
     *
     * @return bool
     */
    public function redirectPreOrder()
    {
        return $this->getConfigData('pre_order');
    }

    /**
     * Can use for order threshold
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function canUseForQuoteThreshold($quote)
    {
        $total = $quote->getBaseGrandTotal();
        $minTotal = $this->getConfigData('min_order_total');
        $maxTotal = $this->getConfigData('max_order_total');
        if (!empty($minTotal) && $total < $minTotal || !empty($maxTotal) && $total > $maxTotal) {
            return false;
        }
        return true;
    }

    /**
     * Check zero total
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function canUseForZeroTotal($quote)
    {
        $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        if ($total < 0.0001 && $this->getCode() != 'free'
            && !($this->canManageRecurringProfiles() && $quote->hasRecurringItems())
        ) {
            return false;
        }
        return true;
    }

    /**
     * Can use for back ordered
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function canUseForBackOrdered($quote)
    {
        return !Mage::helper('affirm')->isDisableQuoteBackOrdered($quote);
    }

    /**
     * Is available method
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return $this->isAvailableForQuote($quote) && parent::isAvailable($quote);
    }

    /**
     * Added verification for quote (compatibility reason)
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailableForQuote($quote = null)
    {
        if ($quote) {
            $shipCountry = $quote->getShippingAddress()->getCountry();
            if (!empty($shipCountry) && !$this->canUseForCountry($shipCountry)) {
                return false;
            }

            if (!$this->canUseForCountry($quote->getBillingAddress()->getCountry())) {
                return false;
            }

            if (!$this->canUseForCurrency($quote->getStore()->getBaseCurrencyCode())) {
                return false;
            }

            if (!$this->canUseCheckout()) {
                return false;
            }

            if (!$this->canUseForQuoteThreshold($quote)) {
                return false;
            }

            if (!$this->canUseForZeroTotal($quote)) {
                return false;
            }

            if (!$this->canUseForBackOrdered($quote)) {
                return false;
            }
        }

        return true;
    }
}
