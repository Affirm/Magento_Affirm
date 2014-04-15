<?php
class Affirm_Affirm_Block_Payment_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $order = $this->getOrder();
        $payment_method = $order->getPayment()->getMethodInstance();

        $html = '<html><body>';
        $html.= '<script type="text/javascript" src="'. $payment_method->getBaseApiUrl() . '/v1.1/affirm.js"></script>';
        $html.= '<script type="text/javascript">affirm.checkout('.json_encode($payment_method->getCheckoutObject($order)).'); affirm.checkout.open();</script>';
        $html.= '</body></html>';
        return $html;
    }
}
