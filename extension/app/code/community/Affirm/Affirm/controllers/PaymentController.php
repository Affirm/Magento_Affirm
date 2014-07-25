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

    public function renderPreOrderAction()
    {
        $order = $this->getRequest()->getParam("order");
        $string = $this->getLayout()->createBlock('affirm/payment_redirect')->setOrder($order)->toHtml();
        $this->_getCheckoutSession()->setPreOrderRender($string);
        $result = array("redirect"=>Mage::getUrl('*/*/redirectPreOrder'));
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function redirectPreOrderAction()
    {
        $this->getResponse()->setBody($this->_getCheckoutSession()->getPreOrderRender());
    }

    public function confirmAction()
    {
        $serialized_request = Mage::getSingleton('checkout/session')->getAffirmOrderRequest();
        $checkout_token = $this->getRequest()->getParam("checkout_token");
        Mage::log("Checkout token is: $checkout_token");

        if ($serialized_request && $checkout_token)
        {
            $proxy_request = unserialize($serialized_request);
            if ($proxy_request != $_SERVER['REQUEST_METHOD'])
            {
                $_SERVER['REQUEST_METHOD'] = $proxy_request["method"];
            }
            if ($proxy_request["method"] == "POST")
            {
                $_POST = $proxy_request["POST"];
            }
            Mage::register("affirm_token_code", $checkout_token);
            $this->_forward($proxy_request["action"], $proxy_request["controller"], $proxy_request["module"], $proxy_request["params"]);

            #need to actually execute the forward!
            $front = Mage::app()->getFrontController();
            $request = $this->getRequest();
            foreach ($front->getRouters() as $router) {
                if ($router->match($request)) {
                    break;
                }
            }

            $orderResult = Mage::helper('core')->jsonDecode($this->getResponse()->getBody());

            if ($orderResult["success"])
            {
                Mage::getSingleton('checkout/session')->setAffirmOrderRequest(null);
                Mage::getSingleton('checkout/session')->setPreOrderRender(null);
                $this->_redirect('checkout/onepage/success');
            }
            elseif($orderResult["error"] && $orderResult["error_messages"])
            {
                Mage::getSingleton('checkout/session')->addError($orderResult["error_messages"]);
                $this->_redirect('checkout/onepage/index');
            }

            return;
        }
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false);
        Mage::getSingleton('checkout/session')->getQuote()->save();
        $session = $this->_getCheckoutSession();
        if (!$checkout_token)
        {
            Mage::throwException($this->__('Confirm has no checkout token.'));
        }

        if ($session->getLastRealOrderId()) {
            $data = $this->getRequest()->getPost(); // TODO(brian): remove dead code
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            $order->getPayment()->getMethodInstance()->processConfirmOrder($order, $checkout_token);

            // TODO(brian): add a boolean configuration option to allow
            // merchants to decide whether affirm should send emails upon email
            // confirmation.
            $order->sendNewOrderEmail();

            $this->_redirect('checkout/onepage/success');
            return;
        }
        $this->_redirect('checkout/onepage');
    }

    // TODO(brian): implement cancel action
}
