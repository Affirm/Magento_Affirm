<?php
class Affirm_Affirm_Block_Payment_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        // TODO(brian): refactor this into a 3 step process to make the state
        // change more explicit
        $this->replaceLabel();
    }

    protected function _toHtml()
    {
        // TODO(brian): extract this html block to a template
        // TODO(brian): extract css
        $msg = "You'll complete your payment after you place your order.";

        $html = "<ul class=\"form-list\" id=\"payment_form_affirm\" style=\"display:none;\">";
        $html .= "<li class=\"form-alt\">";

        // heading
        $html .= "<div style=\"color:#034082; font-size:16px; \">";
        $html .= "How does Affirm work?";
        $html .= "</div>";

        // sub
        $html .= "<div style=\"color:#6f6f6f; font-size:14px; \">";

        $html .= "Just enter your basic information and get approved instantly.";
        $html .= "<br>";
        $html .= "You will complete your payment on the Affirm website";
        $html .= "</div>";

        $html .= "</li>";
        $html .= "</ul>";
        $html .= '<span style="display:none">';
        $html .= Mage::getConfig()->getModuleConfig('Affirm_Affirm')->version;
        $html .= "</span>";

        return $html;
    }

    /* Replaces default label with custom image, conditionally displaying text
     * based on the Affirm product.
     *
     * Context: Payment Information step of Checkout flow
     */
    private function replaceLabel()
    {
        if (Mage::getStoreConfig('payment/affirm/plain_text_title_enabled') == false) {
            $this->setMethodTitle('');
            // TODO(brian): extract html to template
            // TODO(brian): conditionally load based on env config option
            // This is a stopgap until the promo API is ready to go
            $logoSrc = "https://cdn1.affirm.com/images/badges/affirm-logo_78x54.png";
            $html = "<img src=\"" . $logoSrc . "\" width=\"39\" height=\"27\" class=\"v-middle\" />&nbsp;";

            // TODO(brian): conditionally display based on payment type
            // alt message: $html.= "Buy Now and Pay Later";
            $html.= "Buy Now with 3 Easy Payments";

            $this->setMethodLabelAfterHtml($html);
        }
    }
}
