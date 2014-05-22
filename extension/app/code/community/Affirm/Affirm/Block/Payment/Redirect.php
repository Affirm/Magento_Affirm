<?php
class Affirm_Affirm_Block_Payment_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $order = $this->getOrder();
        $payment_method = $order->getPayment()->getMethodInstance();

        $html = '<html><body>';
        $html.= '<script type="text/javascript" src="'. trim($payment_method->getBaseApiUrl(), "/") . '/js/v2/affirm.js"></script>';
        $html.= '<script type="text/javascript">';
        $html.= 'affirm.checkout(' . json_encode($payment_method->getCheckoutObject($order)) . ');';
        $html.= 'affirm.ui.error.on("close", function(){ window.location= "' . Mage::helper('checkout/url')->getCheckoutUrl() . '";});';
        $html.= 'affirm.checkout.post();';
        $html.= '</script>';
        $html.= '</body></html>';
        return $html;
    }
}
