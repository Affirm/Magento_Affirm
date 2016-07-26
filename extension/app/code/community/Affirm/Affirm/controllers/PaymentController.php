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
            ->setBody($this->getLayout()->createBlock('affirm/payment_redirect')->setOrder($order)->toHtml());
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
        $string = $this->getLayout()->createBlock('affirm/payment_redirect')->setOrder($order)->toHtml();
        $serializedRequest = $checkoutSession->getAffirmOrderRequest();
        $proxyRequest = unserialize($serializedRequest);

        //only reserve this order id
        $modQuote = Mage::getModel('sales/quote')->load($quote->getId());
        $modQuote->setReservedOrderId($order->getIncrementId());
        $modQuote->save();

        if (Mage::helper('affirm')->isXhrRequest($proxyRequest)) {
            $checkoutSession->setPreOrderRender($string);
            $result = array('redirect' => Mage::getUrl('affirm/payment/redirectPreOrder'));
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
        $serializedRequest = Mage::helper('affirm')->getCheckoutSession()->getAffirmOrderRequest();
        $checkoutToken = $this->getRequest()->getParam('checkout_token');

        if ($this->_isPlaceOrderAfterConf($serializedRequest, $checkoutToken)) {
            $this->_processConfWithSaveOrder($checkoutToken, $serializedRequest);
        } else {
            $this->_processConfWithoutSaveOrder($checkoutToken);
        }
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

        $proxyRequest = unserialize($serializedRequest);
        $this->getRequest()->setPost($proxyRequest['POST']);
        Mage::register('affirm_token_code', $checkoutToken);
        $this->_forward($proxyRequest['action'], $proxyRequest['controller'], $proxyRequest['module'], $proxyRequest['params']);
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
            $checkoutSession->addError($this->__('Confirm has no checkout token.'));
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
        $this->_redirect('checkout/onepage');
    }
}
