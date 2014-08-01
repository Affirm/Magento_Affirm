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
        $serialized_request = Mage::getSingleton('checkout/session')->getAffirmOrderRequest();
        $proxy_request = unserialize($serialized_request);

        if (isset($proxy_request["xhr"]) && $proxy_request["xhr"])
        {
            $this->_getCheckoutSession()->setPreOrderRender($string);
            $result = array("redirect"=>Mage::getUrl('*/*/redirectPreOrder'));
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
        else
        {
            $this->getResponse()->setBody($string);
        }
    }

    public function redirectPreOrderAction()
    {
        $this->getResponse()->setBody($this->_getCheckoutSession()->getPreOrderRender());
    }

    public function confirmAction()
    {
        $serialized_request = Mage::getSingleton('checkout/session')->getAffirmOrderRequest();
        $checkout_token = $this->getRequest()->getParam("checkout_token");

        if ($serialized_request && $checkout_token)
        {
            if (Mage::getSingleton('checkout/session')->getLastAffirmSuccess() == $checkout_token)
            {
                Mage::getSingleton('checkout/session')->addSuccess("This order was already completed.");
                //Go directly to success page if this is already successful
                $this->_redirect('checkout/onepage/success');
                return;
            }

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


            if ((isset($proxy_request["xhr"]) && $proxy_request["xhr"]))
            {
                #need to actually execute the forward!
                $front = Mage::app()->getFrontController();
                $request = $this->getRequest();
                foreach ($front->getRouters() as $router) {
                    if ($router->match($request)) {
                        break;
                    }
                }

                try {
                    $orderResult = Mage::helper('core')->jsonDecode($this->getResponse()->getBody());
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::getSingleton('checkout/session')->addError("Error processing affirm order");
                    $this->_redirect('checkout/cart');
                    return;
                }

                if (isset($orderResult["success"]) && $orderResult["success"])
                {
                    Mage::getSingleton('checkout/session')->setPreOrderRender(null);
                    Mage::getSingleton('checkout/session')->setLastAffirmSuccess($checkout_token);
                    $this->_redirect('checkout/onepage/success');
                }
                elseif(isset($orderResult["error_messages"]) && $orderResult["error"] && $orderResult["error_messages"])
                {
                    Mage::getSingleton('checkout/session')->addError($orderResult["error_messages"]);
                    $this->_redirect('checkout/cart');
                }
                else
                {
                    // Very rarely, a merchant's extensively customized Checkout
                    // extension may be incompatible with the Affirm extension.
                    // To help discover this issue during testing, provide a
                    // useful message.
                    Mage::log("Customer tried to checkout using Affirm.
                               The order could not be saved.
                               Your Checkout extension may not be compatible with this
                               version of the Affirm Extension.
                               Please contact Affirm Developer Support for more info");

                    Mage::getSingleton('checkout/session')->addError("Error encountered while processing affirm order");
                    $this->_redirect('checkout/cart');
                    return;
                }
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
