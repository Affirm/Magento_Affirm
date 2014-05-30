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
        $html .= "Pay over time in 3 easy payments";
        $html .= "</div>";

        // sub
        $html .= "<div style=\"color:#6f6f6f; font-size:14px; \">";
        $html .= "just enter your basic details and get";
        $html .= "<br>";
        $html .= "approved instantly.";
        $html .= "</div>";

        $html .= "</li>";
        $html .= "</ul>";

        return $html;
    }

    /* Replaces default label with custom image, conditionally displaying text
     * based on the Affirm product.
     *
     * Context: Payment Information step of Checkout flow
     */
    private function replaceLabel()
    {
        $this->setMethodTitle(""); // removes default title

        // TODO(brian): extract html to template
        // TODO(brian): conditionally load based on env config option
        // This is a stopgap until the promo API is ready to go
        $logoSrc = "https://cdn1.affirm.com/images/badges/affirm-card_78x54.png";
        $html = "<img src=\"" . $logoSrc . "\" width=\"39\" height=\"27\" class=\"v-middle\" />&nbsp;";

        // TODO(brian): conditionally display based on payment type
        // alt message: $html.= "Buy Now and Pay Later";
        $html.= "3 Monthly Payments with Split Pay";

        $this->setMethodLabelAfterHtml($html);
    }
}
