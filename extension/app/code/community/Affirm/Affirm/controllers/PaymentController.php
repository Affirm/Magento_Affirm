<?php

class Affirm_Affirm_PaymentController extends Mage_Core_Controller_Front_Action
{

    private function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    // TODO why is this still here?
    private function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    public function redirectAction()
    {
        $session = $this->_getCheckoutSession();
        if (!$session->getLastRealOrderId())
        {
            $session->addError($this->__('Your order has expired.'));
            $this->_redirect('checkout/cart');
            return;
        }
        $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('affirm/payment_redirect')->setOrder($order)->toHtml());
        $session->unsQuoteId();
        $session->unsRedirectUrl();
    }


    public function confirmAction()
    {
        $session = $this->_getCheckoutSession();
        $checkout_token = $this->getRequest()->getParam("checkout_token");
        if (!$checkout_token)
        {
            Mage::throwException($this->__('Confirm has no checkout token.'));
        }

        if ($session->getLastRealOrderId()) {
            $data = $this->getRequest()->getPost(); // TODO(brian): remove dead code
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            $order->getPayment()->getMethodInstance()->processConfirmOrder($order, $checkout_token);
            $order->sendNewOrderEmail();
            $this->_redirect('checkout/onepage/success');
            return;
        }
        $this->_redirect('checkout/onepage');
    }

    // TODO(brian): implement cancel action
}
