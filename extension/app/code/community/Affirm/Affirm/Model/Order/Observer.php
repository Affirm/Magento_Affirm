<?php
class Affirm_Affirm_Model_Order_Observer
{
    public function reactivate_quote($observer)
    {
        // TODO(brian): get object once
        // TODO(brian): don't hardcode the payment method code. get it from an 
        // instance of the model object
        if (Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethodInstance()->getCode() == 'affirm') {
            Mage::getSingleton('checkout/session')->getQuote()->setIsActive(true);
            Mage::getSingleton('checkout/session')->getQuote()->save();
        }
    }
}
