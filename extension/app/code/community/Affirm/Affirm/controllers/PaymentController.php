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
require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'OnepageController.php';
/**
 * Class Affirm_Affirm_PaymentController
 */
class Affirm_Affirm_PaymentController extends Mage_Checkout_OnepageController
{
    /**
     * Redirect
     */
    public function redirectAction()
    {
        $session = Mage::helper('affirm')->getCheckoutSession();
        if (!$session->getLastRealOrderId()) {
            $session->addError($this->__('Your order has expired.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
        $this->getResponse()
            ->setBody($this->getLayout()->createBlock('affirm/payment_redirect', 'affirm_redirect')
                ->setOrder($order)->toHtml()
            );
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }

    /**
     * Render pre order
     */
    public function renderPreOrderAction()
    {
        //after place order
        $order = $this->getRequest()->getParam('order');
        $quote = $this->getRequest()->getParam('quote');
        $checkoutSession = Mage::helper('affirm')->getCheckoutSession();
        $string = $this->getLayout()->createBlock('affirm/payment_redirect', 'affirm_redirect')
            ->setOrder($order)->toHtml();
        $serializedRequest = $checkoutSession->getAffirmOrderRequest();
        $proxyRequest = json_decode($serializedRequest,true);
        //only reserve this order id
        $modQuote = Mage::getModel('sales/quote')->load($quote->getId());
        $modQuote->setReservedOrderId($order->getIncrementId());
        $modQuote->save();
        if (Mage::helper('affirm')->isXhrRequest($proxyRequest)) {
            $checkoutSession->setPreOrderRender($string);
            $result = array('redirect' => Mage::getUrl('affirm/payment/redirectPreOrder',
                array('_secure' => $this->getRequest()->isSecure())
            ));
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        } else {
            $this->getResponse()->setBody($string);
        }
    }

    /**
     * Redirect pre order
     */
    public function redirectPreOrderAction()
    {
        $this->getResponse()->setBody(Mage::helper('affirm')->getCheckoutSession()->getPreOrderRender());
    }

    /**
     * Is place order after confirm
     *
     * @param string $serializedRequest
     * @param string $checkoutToken
     * @return bool
     */
    protected function _isPlaceOrderAfterConf($serializedRequest, $checkoutToken)
    {
        return $serializedRequest && $checkoutToken;
    }

    /**
     * Confirm checkout
     */
    public function confirmAction()
    {
        $checkoutToken = $this->getRequest()->getParam('checkout_token');
        $checkoutSession = Mage::helper('affirm')->getCheckoutSession();
        if (!$checkoutToken) {
            $checkoutSession->addError($this->__('Error encountered during checkout. Confirm has no checkout token.'));
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
            return;
        }
        $serializedRequest = $checkoutSession->getAffirmOrderRequest();
        if (Mage::helper('affirm')->isCheckoutFlowTypeModal()) {
            $this->_processConfWithSaveOrderModalCheckout($checkoutToken, $serializedRequest);
        } else {

            if ($this->_isPlaceOrderAfterConf($serializedRequest, $checkoutToken)) {
                $this->_processConfWithSaveOrder($checkoutToken, $serializedRequest);
            } else {
                $this->_processConfWithoutSaveOrder($checkoutToken);
            }
        }
    }

    /**
     * Process conf with save order for modal checkout
     *
     * @param string $checkoutToken
     * @param string $serializedRequest
     */
    protected function _processConfWithSaveOrderModalCheckout($checkoutToken, $serializedRequest)
    {
        $checkoutSession = Mage::helper('affirm')->getCheckoutSession();
        if ($checkoutSession->getLastAffirmSuccess() == $checkoutToken) {
            $checkoutSession->addSuccess($this->__('This order was already completed.'));
            //Go directly to success page if this is already successful
            $this->_redirect('checkout/onepage/success');
            return;
        }
        $proxyRequest = json_decode($serializedRequest,true);
        $this->getRequest()->setPost($proxyRequest['POST']);
        Mage::register('affirm_token_code', $checkoutToken);
        $this->_forward($proxyRequest['action'], $proxyRequest['controller'], $proxyRequest['module'], $proxyRequest['params']);
    }

    /**
     * Process conf with save order
     *
     * @param string $checkoutToken
     * @param string $serializedRequest
     */
    protected function _processConfWithSaveOrder($checkoutToken, $serializedRequest)
    {
        $checkoutSession = Mage::helper('affirm')->getCheckoutSession();
        if ($checkoutSession->getLastAffirmSuccess() == $checkoutToken) {
            $checkoutSession->addSuccess($this->__('This order was already completed.'));
            //Go directly to success page if this is already successful
            $this->_redirect('checkout/onepage/success');
            return;
        }
        $proxyRequest = json_decode($serializedRequest, true);
        $this->getRequest()->setPost($proxyRequest['POST']);
        Mage::register('affirm_token_code', $checkoutToken);
        $this->_forward($proxyRequest['action'], $proxyRequest['controller'], $proxyRequest['module'], $proxyRequest['params']);

        // Fix for rewrite modules paths
        // Change the routing info → No Events (It will now trigger then checkout post dispatch event)
        $this->getRequest()->setRoutingInfo($proxyRequest['routing_info']);
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
        // End change
    }

    /**
     * Process conf without save order
     *
     * @param string $checkoutToken
     * @throws Affirm_Affirm_Exception
     */
    protected function _processConfWithoutSaveOrder($checkoutToken)
    {
        $checkoutSession = Mage::helper('affirm')->getCheckoutSession();
        if (!$checkoutToken) {
            $checkoutSession->addError($this->__('Error encountered during checkout. Confirm has no checkout token.'));
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
            return;
        }
        try {
            if ($checkoutSession->getLastRealOrderId()) {
                $order = Mage::getModel('sales/order')
                    ->loadByIncrementId($checkoutSession->getLastRealOrderId());
                $order->getPayment()->getMethodInstance()->processConfirmOrder($order, $checkoutToken);
                $order->sendNewOrderEmail();
                $this->_redirect('checkout/onepage/success');
                return;
            }
        } catch (Affirm_Affirm_Exception $e) {
            Mage::logException($e);
            $checkoutSession->addError($e->getMessage());
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $checkoutSession->addError($this->__('Error encountered while processing affirm order.'));
            $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'))->sendResponse();
            return;
        }

        $this->_redirect('checkout/onepage');
    }

    /**
     * Set Affirm Payment Flag And Checkout
     */
    public function setPaymentFlagAndCheckoutAction()
    {
        Mage::helper('affirm')->getCheckoutSession()->setAffirmPaymentFlag(true);
        $this->_redirectUrl(Mage::helper('checkout/url')->getCheckoutUrl());
    }

    /**
     * Get checkout object from quote
     *
     * @return array
     */
    public function processCheckoutQuoteObjectAction()
    {
        $post = $this->getRequest()->getPost();
        $checkoutSession = Mage::helper('affirm')->getCheckoutSession();
        $checkoutSession->setCartWasUpdated(false);
        if($post) {
            $billingData  = $this->getRequest()->getPost('billing', array());
            $result       = $this->createAccountWhenCheckout($billingData);
            if ($result['success']) {
                $updatedSection = $this->getRequest()->getPost('updated_section', null);
                switch ($updatedSection) {
                    case "billing":
                        $this->_saveBilling();
                        break;
                    case "shipping":
                        $this->_saveShipping();
                        break;
                    case "shipping_method":
                        $this->_saveShippingMethod();
                        break;
                    case "payment_method":
                        $this->_savePaymentMethod();
                        break;
                    default:
                        $this->_saveBilling();
                        $this->_saveShippingMethod();
                        $this->_savePaymentMethod();
                        break;
                }
            } else {
                $result['success']    = false;
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                return;
            }
        }
        $this->getOnepage()->getQuote()->setTotalsCollectedFlag(false);
        $quote = $checkoutSession->getQuote();
        $shippingAddress = $quote->getShippingAddress();
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
        $billingAddress = $quote->getBillingAddress();
        $billing = array(
            'name' => array('full' => $billingAddress->getName()),
            'email' => $quote->getCustomerEmail(),
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
        foreach ($quote->getAllVisibleItems() as $orderItem) {
            $productIds[] = $orderItem->getProductId();
        }
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(
                array('affirm_product_mfp', 'affirm_product_mfp_type', 'affirm_product_mfp_priority')
            )
            ->addAttributeToFilter('entity_id', array('in' => $productIds));
        $productItems = $products->getItems();
        foreach ($quote->getAllVisibleItems() as $orderItem) {
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
            'checkout_id' => $quote->getId(),
            'currency' => $quote->getQuoteCurrencyCode(),
            'shipping_amount' => Mage::helper('affirm/util')->formatCents($quote->getShippingAddress()->getShippingAmount()),
            'shipping_type' => $quote->getShippingAddress()->getShippingMethod(),
            'tax_amount' => Mage::helper('affirm/util')->formatCents($quote->getShippingAddress()->getTaxAmount()),
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
        $discountAmtAffirm = (-1) * $quote->getDiscountAmount();
        if ($discountAmtAffirm > 0.001) {
            $discountCode = $this->_getDiscountCode($quote);
            $checkout['discounts'] = array(
                $discountCode => array(
                    'discount_amount' => Mage::helper('affirm/util')->formatCents($discountAmtAffirm)
                )
            );
        }

        if ($shipping) {
            $checkout['shipping'] = $shipping;
        }
        $checkout['total'] = Mage::helper('affirm/util')->formatCents($quote->getGrandTotal());
        if (method_exists('Mage', 'getEdition')){
            $platform_edition = Mage::getEdition();
        }
        $platform_version = Mage::getVersion();
        $platform_version_edition = isset($platform_edition) ? $platform_version.' '.$platform_edition : $platform_version;
        $checkout['metadata'] = array(
            'shipping_type' => $quote->getShippingAddress()->getShippingDescription(),
            'platform_type' => 'Magento',
            'platform_version' => $platform_version_edition,
            'platform_affirm' => Mage::helper('affirm')->getExtensionVersion(),
            'mode' => 'modal'
        );
        $affirmMFPValue = Mage::helper('affirm/mfp')->getAffirmMFPValue($productItemsMFP, $categoryItemsIds, $quote->getBaseGrandTotal());
        if ($affirmMFPValue) {
            $checkout['financing_program'] = $affirmMFPValue;
        }

        $checkoutObject = new Varien_Object($checkout);
        Mage::dispatchEvent('affirm_get_checkout_object_after', array('checkout_object' => $checkoutObject));
        $checkout = $checkoutObject->getData();
        $result = array(
            'success' => true,
            'error'   => false,
            'checkout' => $checkout
        );
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _saveBilling(){
        $billing = $this->getRequest()->getPost('billing', array());
        $this->saveMethodAction();
        $this->saveBillingAction();
        $usingShippingCase = isset($billing['use_for_shipping']) ? (int)$billing['use_for_shipping'] : 0;

        if (!$usingShippingCase)
            $this->saveShippingAction();
    }

    protected function _saveShipping(){
        $this->saveShippingAction();
    }

    protected function _saveShippingMethod(){
        $this->saveShippingMethodAction();
    }

    protected function _savePaymentMethod(){
        $this->savePaymentAction();
    }

    /**
     * Create accont when customer checkout
     *
     * @return array
     */
    public function createAccountWhenCheckout($billingData)
    {
        $result = array(
            'success'  => true,
            'messages' => array(),
        );
        if (!$this->getOnepage()->getCustomerSession()->isLoggedIn()) {
            if (isset($billingData['create_account'])) {
                $this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER);
            } else {
                $this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_GUEST);
            }
        }

        if (!$this->getOnepage()->getQuote()->getCustomerId() &&
            Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER == $this->getOnepage()->getQuote()->getCheckoutMethod()
        ) {
            if ($this->_customerEmailExists($billingData['email'], Mage::app()->getWebsite()->getId())) {
                $result['success']    = false;
                $result['messages'][] = $this->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.');
            }
        }
        return $result;
    }

    /**
     * @reference Mage_Checkout_OnepageController
     * @param string $email
     * @param int    $websiteId
     * @return false|Mage_Customer_Model_Customer
     */
    protected function _customerEmailExists($email, $websiteId = null)
    {
        $customer = Mage::getModel('customer/customer');
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }

        return false;
    }
}
