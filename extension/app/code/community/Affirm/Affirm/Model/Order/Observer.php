<?php
class Affirm_Order_Save_Redirector extends Mage_Core_Controller_Varien_Exception
{
    private $order;
    private $quote;

    public function __construct($order, $quote)
    {
        $this->order = $order;
        $this->quote = $quote;
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

class Affirm_Order_Payment_Redirector extends Mage_Payment_Model_Info_Exception
{
    private $order;
    private $quote;

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
            throw new Affirm_Order_Save_Redirector($this->order, $this->quote);
        }
    }

    public function __tostring()
    {
        throw new Affirm_Order_Save_Redirector($this->order, $this->quote);
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
                Mage::register("affirm_order_request", $order_request);
                throw new Affirm_Order_Payment_Redirector($order, $quote);
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
        $method_inst = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance();
        if ($method_inst->getCode() == 'affirm' && !$method_inst->redirectPreOrder()) {
            Mage::getSingleton('checkout/session')->getQuote()->setIsActive(true);
            Mage::getSingleton('checkout/session')->getQuote()->save();
        }
    }
}
