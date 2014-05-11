<?php
class Affirm_Affirm_Model_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Affirm_Affirm_Model_Payment::ACTION_AUTHORIZE,
                'label' => Mage::helper('affirm')->__('Authorize Only')
            ),
            array(
                'value' => Affirm_Affirm_Model_Payment::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('affirm')->__('Authorize and Capture')
            ),
        );
    }
}
