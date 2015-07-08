<?php
class Affirm_Order_Save_Redirector extends Mage_Core_Controller_Varien_Exception
{
    public $order;
    public $quote;

    public function __construct($order, $quote)
    {
        $this->order = $order;
        $this->quote = $quote;
        unset($this->message);
    }

    public function __get($property)
    {
        if ($property == "message")
        {
            throw $this;
        }
    }

    public function __tostring()
    {
        throw $this;
    }

    public function getResultFlags()
    {
        return array();
    }

    public function getResultCallback()
    {
        return array(Mage_Core_Controller_Varien_Exception::RESULT_FORWARD, array("renderPreOrder", "payment", "affirm", array("order"=>$this->order, "quote"=>$this->quote)));
    }
}


class Affirm_Affirm_Model_Order_Observer
{

    public function pre_order($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        $method_inst = $order->getPayment()->getMethodInstance();
        $token_code = Mage::registry("affirm_token_code");
        if ($token_code)
        {
            $method_inst->setAffirmCheckoutToken($token_code);
        }

        if ($method_inst->getCode() == "affirm" && $method_inst->redirectPreOrder())
        {
            if(!$token_code)
            {
                #ok record the current controller that we are using...
                $request = Mage::app()->getRequest();
                $order_request = array("action"=>$request->getActionName(),
                                "controller"=>$request->getControllerName(),
                                "module"=>$request->getModuleName(),
                                "params"=>$request->getParams(),
                                "method"=>$request->getMethod(),
                                "xhr"=>$request->isXmlHttpRequest(),
                                "POST"=>$_POST, #need post for some cross site issues
                                "quote_id"=>$quote->getId());                
                Mage::getSingleton('checkout/session')->setAffirmOrderRequest(serialize($order_request));
                throw new Affirm_Order_Save_Redirector($order, $quote);
            }
        }
        elseif ($method_inst->getCode() == "affirm" && !$method_inst->redirectPreOrder())
        {
            #clear the Order request if we are not using this thing..
            Mage::getSingleton('checkout/session')->setAffirmOrderRequest(null);
        }
    }

    public function reactivate_quote($observer)
    {
        // TODO(brian): get object once
        // TODO(brian): don't hardcode the payment method code. get it from an 
        // instance of the model object
        $method_inst = $observer->getQuote()->getPayment()->getMethodInstance();
        if ($method_inst->getCode() == 'affirm' && !$method_inst->redirectPreOrder()) {
            Mage::getSingleton('checkout/session')->getQuote()->setIsActive(true);
            Mage::getSingleton('checkout/session')->getQuote()->save();
        }
    }
}
