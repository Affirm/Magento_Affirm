<?php
class Affirm_Affirm_Model_Source_PaymentCheckoutXhr
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Affirm_Affirm_Model_Payment::CHECKOUT_XHR_AUTO,
                'label' => Mage::helper('affirm')->__('Auto Detect')
            ),
            array(
                'value' => Affirm_Affirm_Model_Payment::CHECKOUT_XHR,
                'label' => Mage::helper('affirm')->__('Checkout uses xhr')
            ),
            array(
                'value' => Affirm_Affirm_Model_Payment::CHECKOUT_REDIRECT,
                'label' => Mage::helper('affirm')->__('Checkout uses redirect')
            )
        );
    }
}
