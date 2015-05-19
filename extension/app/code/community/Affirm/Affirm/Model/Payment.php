<?php

require_once Mage::getBaseDir('lib').DS.'Affirm'.DS.'Affirm.php';

class Affirm_Affirm_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    // TODO(brian): extract this along with API client 
    const API_CHARGES_PATH = '/api/v2/charges/';
    const API_CHECKOUT_PATH = '/api/v2/checkout/';

    const CHECKOUT_XHR_AUTO = 'auto';
    const CHECKOUT_XHR = 'xhr';
    const CHECKOUT_REDIRECT = 'redirect';

    /**
     * Form block type
     */
    protected $_formBlockType = 'affirm/payment_form';

    /**
     * Info block type
     */
    protected $_infoBlockType = 'affirm/payment_info';


    const CHECKOUT_TOKEN = 'checkout_token';
    const METHOD_CODE = 'affirm';
    protected $_code  = self::METHOD_CODE;

    /**
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

    protected $_allowCurrencyCode = array('USD');

    /**
     * Check method for processing with base currency
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
    	// TODO(brian): extract implementation to separate class
        if (!in_array($currencyCode, $this->getAcceptedCurrencyCodes())) {
            return false;
        }
        return true;
    }


    public function isInitializeNeeded()
    {
        if ($this->getCheckoutToken())
        {
            return false;
        }
        else
        {
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

    public function getChargeId()
    {
        return $this->getInfoInstance()->getAdditionalInformation("charge_id");
    }

    public function getCheckoutToken()
    {
        return $this->getInfoInstance()->getAdditionalInformation(self::CHECKOUT_TOKEN);
    }

    protected function setChargeId($charge_id)
    {
        return $this->getInfoInstance()->setAdditionalInformation("charge_id", $charge_id);
    }

    public function getBaseApiUrl()
    {
        return $this->getConfigData('api_url');
    }

    // TODO(brian): extract to a separate class and use DI to make it testable/mockable
    public function _api_request($method, $path, $data=null, $resource_path=self::API_CHARGES_PATH)
    {
        $url = trim($this->getBaseApiUrl(), "/") . $resource_path . $path;
        Mage::log($url);

        $client = new Zend_Http_Client($url);

        if ($method == Zend_Http_Client::POST && $data)
        {
            $json = json_encode($data);
            $client->setRawData($json, 'application/json');
        }
        
        $client->setAuth($this->getConfigData('api_key'), $this->getConfigData('secret_key'), Zend_Http_Client::AUTH_BASIC);

        $raw_result = $client->request($method)->getRawBody();
        try{
            $ret_json = Zend_Json::decode($raw_result, Zend_Json::TYPE_ARRAY);
        } catch(Zend_Json_Exception $e)
        {
            Mage::throwException(Mage::helper('affirm')->__('Invalid affirm response: '. $raw_result));
        }

        //validate to make sure there are no errors here
        if (isset($ret_json["status_code"]))
        {
            Mage::throwException(Mage::helper('affirm')->__('Affirm error code:'. $ret_json["status_code"] . ' error: '. $ret_json["message"]));
        }
        return $ret_json;
    }

    private function _get_checkout_from_token($token)
    {
        return $this->_api_request('GET', $token, null, self::API_CHECKOUT_PATH);
    }

    private function _get_checkout_total_from_token($token)
    {
        $res = $this->_get_checkout_from_token($token);
        return $res['total'];
    }

    protected function _set_charge_result($result)
    {
        if (isset($result["id"]))
        {
            $this->setChargeId($result["id"]);
        }
        else
        {
            Mage::throwException(Mage::helper('affirm')->__('Affirm charge id not returned from call.'));
        }
    }

    protected function _validate_amount_result($amount, $affirm_amount)
    {
        if ($affirm_amount != $amount)
        {
            Mage::throwException(Mage::helper('affirm')->__('Your cart amount has changed since starting your Affirm application. Please try again.'));
        }
    }

    /**
     * Send capture request to gateway
     *
     * @param Mage_Payment_Model_Info $payment
     * @param decimal $amount
     * @return Mage_Paygate_Model_Authorizenet
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('affirm')->__('Invalid amount for capture.'));
        }
        $charge_id = $this->getChargeId();
        $amount_cents = Affirm_Util::formatCents($amount);
        if (!$charge_id) {
            if ($this->getCheckoutToken())
            {
                $this->authorize($payment, $amount);
                $charge_id = $this->getChargeId();
            }
            else
            {
                Mage::throwException(Mage::helper('affirm')->__('Charge id have not been set.'));
            }
        }
        $result = $this->_api_request(Varien_Http_Client::POST, "{$charge_id}/capture");
        $this->_validate_amount_result($amount_cents, $result["amount"]);
        return $this;
    }

    /**
     * Refund capture
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return Mage_Paypal_Model_Direct
     */
    public function refund(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('affirm')->__('Invalid amount for refund.'));
        }
        $charge_id = $this->getChargeId();
        $amount_cents = Affirm_Util::formatCents($amount);
        if (!$charge_id) {
            Mage::throwException(Mage::helper('affirm')->__('Charge id have not been set.'));
        }
        $result = $this->_api_request(Varien_Http_Client::POST, "{$charge_id}/refund", array(
									"amount"=>$amount_cents)
        );
        $this->_validate_amount_result($amount_cents, $result["amount"]);

        return $this;
    }

    public function void(Varien_Object $payment)
    {
        if (!$this->canVoid($payment)) {
            Mage::throwException(Mage::helper('payment')->__('Void action is not available.'));
        }
        $charge_id = $this->getChargeId();
        if (!$charge_id) {
            Mage::throwException(Mage::helper('affirm')->__('Charge id have not been set.'));
        }
        $result = $this->_api_request(Varien_Http_Client::POST, "{$charge_id}/void");
        return $this;
    }

    /**
     * Send authorize request to gateway
     *
     * @param  Mage_Payment_Model_Info $payment
     * @param  decimal $amount
     * @return Mage_Paygate_Model_Authorizenet
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if ($amount <= 0) {
            Mage::throwException(Mage::helper('affirm')->__('Invalid amount for authorization.'));
        }

        $amount_cents = Affirm_Util::formatCents($amount);
        $token = $payment->getAdditionalInformation(self::CHECKOUT_TOKEN);
        $amount_to_authorize = $this->_get_checkout_total_from_token($token); 
        $this->_validate_amount_result($amount_cents, $amount_to_authorize);

        $result = $this->_api_request(Varien_Http_Client::POST, "", array(
									self::CHECKOUT_TOKEN=>$token)
					);

        $this->_set_charge_result($result);
        $payment->setTransactionId($this->getChargeId())->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Instantiate state and set it to state object
     * @param string $paymentAction
     * @param Varien_Object
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

    public function setAffirmCheckoutToken($checkout_token)
    {
        $payment = $this->getInfoInstance();
        $payment->setAdditionalInformation(self::CHECKOUT_TOKEN, $checkout_token);
    }


    public function processConfirmOrder($order, $checkout_token)
    {
        $payment = $order->getPayment();

        $payment->setAdditionalInformation(self::CHECKOUT_TOKEN, $checkout_token);
        $action = $this->getConfigData('payment_action');

        //authorize the total amount.
        Affirm_Affirm_Model_Payment::authorizePaymentForOrder($payment, $order);
        $payment->setAmountAuthorized(Affirm_Affirm_Model_Payment::_affirmTotal($order));
        $order->save();
        //can capture as well..
        if ($action == self::ACTION_AUTHORIZE_CAPTURE)
        {
            $payment->setAmountAuthorized(Affirm_Affirm_Model_Payment::_affirmTotal($order));

            // TODO(brian): It is unclear why this statement is here. If you
            // know why, please replace this message with documentation to
            // justify its existence.
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
        if(!$this->redirectPreOrder())
        {
            return Mage::getUrl('affirm/payment/redirect', array('_secure' => true));
        }
    }

    public function formatCents($currency, $amount)
    {
        return Affirm_Util::formatCents($amount);
    }

    public function getCheckoutObject($order)
    {
        $info = $this->getInfoInstance(); // TODO(brian): remove unused variable
        $shipping_address = $order->getShippingAddress();
        $shipping = null;
        if ($shipping_address)
        {
            $shipping = array(
                "name"=> array("full"=>$shipping_address->getName()),
                "phone_number"=> $shipping_address->getTelephone(),
                "phone_number_alternative"=> $shipping_address->getAltTelephone(),
                "address"=> array(
                        "line1" => $shipping_address->getStreet(1),
                        "line2" => $shipping_address->getStreet(2),
                        "city" => $shipping_address->getCity(),
                        "state" => $shipping_address->getRegion(),
                        "country" => $shipping_address->getCountryModel()->getIso2Code(),
                        "zipcode" => $shipping_address->getPostcode(),
                      ));
        }

        $billing_address = $order->getBillingAddress();
        $billing = array(
                "email"=>$order->getCustomerEmail(),
                "name"=> array("full"=>$billing_address->getName()),
                "phone_number"=> $billing_address->getTelephone(),
                "phone_number_alternative"=> $billing_address->getAltTelephone(),
                "address"=> array(
                        "line1" => $billing_address->getStreet(1),
                        "line2" => $billing_address->getStreet(2),
                        "city" => $billing_address->getCity(),
                        "state" => $billing_address->getRegion(),
                        "country" => $billing_address->getCountryModel()->getIso2Code(),
                        "zipcode" => $billing_address->getPostcode(),
                      ));

        $items = array();
        $currency = $order->getOrderCurrency();
        $products = Mage::getModel('catalog/product');
        // TODO(brian): instantiate |pricer| upon construction
        $pricer = Mage::getModel('affirm/pricer');
        foreach($order->getAllVisibleItems() as $order_item)
        {
            $productId = $order_item->getProductId();
            $product = $products->load($productId);

            $items[] = array(
                "sku" => $order_item->getSku(),
                "display_name" => $order_item->getName(),
                "item_url" => $product->getProductUrl(),
                "item_image_url" => $product->getImageUrl(),
                "qty" => intval($order_item->getQtyOrdered()),
                "unit_price" => $pricer->getPriceInCents($order_item)
            );
        }

        // TODO(brian): test checkout/onepage urls. it's unclear whether this
        // is enabled for all merchants or whether merchant customization could
        // cause this to be an invalid destination
        $checkout = array(
            'checkout_id'=>$order->getIncrementId(),
            'currency'=>$order->getOrderCurrencyCode(),
            'shipping_amount'=>$this->formatCents($currency, $order->getShippingAmount()),
            'shipping_type'=>$order->getShippingMethod(),
            'tax_amount'=>$this->formatCents($currency, $order->getTaxAmount()),
            "merchant" => array(
                    "public_api_key"=>$this->getConfigData('api_key'), 
                    "user_confirmation_url"=>Mage::getUrl('affirm/payment/confirm', array('_secure' => true)),
                    "user_cancel_url"=>Mage::helper('checkout/url')->getCheckoutUrl(),
                    "charge_declined_url"=>Mage::helper('checkout/url')->getCheckoutUrl()
                  ),
            "config" => array("required_billing_fields"=> "name,address,email"),
            "items" => $items,
            "billing" => $billing);

        // By convention, Affirm expects positive value for discount amount.
        // Magento provides negative.
        $discountAmtAffirm = -1 * $order->getDiscountAmount();
        if ($discountAmtAffirm > 0.001)
        {
          $checkout["discounts"] = array(
            $order->getCouponCode()=>array(
              "discount_amount"=>$this->formatCents($currency, $discountAmtAffirm)
            )
          );
        }

        if ($shipping)
        {
            $checkout["shipping"] = $shipping;
        }
        $checkout['financial_product_key'] = $this->getConfigData('financial_product_key');

        // TODO(brian): make this safer and less error-prone.
        $checkout['total'] = Affirm_Util::formatCents(Affirm_Affirm_Model_Payment::_affirmTotal($order));
        $checkout['meta'] = Affirm_Affirm_Model_Payment::_getMetadata();
        return $checkout;
    }

    // TODO(brian): extract string name constant
    private static function _getMetadata()
    {
        $session = Mage::getSingleton('customer/session');
        $meta = array(
            "source" => array(
                "data" => array(
                    "is_logged_in" => $session->isLoggedIn(),
                    "magento_version" => Mage::getVersion()
                ),
                "client_name" => "magento_affirm",
                "version" => Mage::getConfig()->getModuleConfig('Affirm_Affirm')->version
            )
        );

        if (Mage::app()->getStore()->isAdmin()) {
            //this is in the admin area..
            $meta["source"]["merchant_user_initiated"] = 1;
            $user = Mage::getSingleton('admin/session')->getUser();
            if ($user) {
                $meta["source"]["data"]["merchant_logged_in"] = 1;
                $meta["source"]["data"]["merchant_username"] = $user->getUsername();
            }
        }

        if ($session->isLoggedIn()) {
            $customerId = $session->getCustomerId();

            $customer = Mage::getModel('customer/customer')->load($customerId);
            $meta['source']['data']['account_created'] = Mage::getModel('core/date')->
                date(Zend_Date::ISO_8601, $customer->getCreatedAt());

            $orders = Mage::getModel('sales/order')->getCollection()->
                addFilter('customer_id', $customerId)->
                setOrder('created_at', Varien_Data_Collection_Db::SORT_ORDER_DESC);
            $orderCount = $orders->count();
            $meta['data']['order_count'] = $orderCount;

            if ($orderCount > 0) {
                $meta['data']['last_order_date'] = Mage::getModel('core/date')->
                date(Zend_Date::ISO_8601, $orders->getFirstItem()->getCreatedAt());
            }
        }
        return $meta;
    }

    /* A hacky thing used to access a private method (authorize(...)) on the
     * payment object in order to provide compatibility with version 1.4.0.1 CE.
     *
     * FIXME(brian): take a closer look at the payment class at version 1.4.
     * Surely, there _must_ be a way to accomplish this without reflection.
     *
     * TODO(brian): Write a regression test to catch incompatibilities with
     * other Magento versions.
     */
    private static function authorizePaymentForOrder($payment, $order)
    {
      $moduleVersion = Mage::getConfig()->getModuleConfig("Mage_Sales")->version;
      $incompatibleVersions = array(
        "0.9.56"
      );
      if (in_array($moduleVersion, $incompatibleVersions)) {
        Affirm_Affirm_Model_Payment::callPrivateMethod($payment, "_authorize", true, Affirm_Affirm_Model_Payment::_affirmTotal($order));
      } else {
        $payment->authorize(true, Affirm_Affirm_Model_Payment::_affirmTotal($order));
      }
    }

    // TODO(brian): move this function to a helper library
    private static function callPrivateMethod($object, $methodName)
    {
      $reflectionClass = new ReflectionClass($object);
      $reflectionMethod = $reflectionClass->getMethod($methodName);
      $reflectionMethod->setAccessible(true);

      $params = array_slice(func_get_args(), 2); //get all the parameters after $methodName
      return $reflectionMethod->invokeArgs($object, $params);
    }

    // TODO(brian): move this to an external pricer so merchants can override
    // the functionality.
    private static function _affirmTotal($order)
    {
        return $order->getTotalDue();
    }

    public function redirectPreOrder()
    {
        return $this->getConfigData('pre_order');
    }
}
