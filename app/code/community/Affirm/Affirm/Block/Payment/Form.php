<?php
class Affirm_Affirm_Block_Payment_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->replaceLabel();
    }

    protected function _toHtml()
    {
      // TODO(brian): extract this html block to a template
      $msg = "You will be sent to the Affirm website to complete your payment.";
      $html = "<ul class=\"form-list\" id=\"payment_form_affirm\" style=\"display:none;\">";
      $html .= "<li class=\"form-alt\">" . $msg . "</li>";
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
        $logoSrc = Mage::getDesign()->getSkinUrl('images/affirm/affirm-card.png');
        $html = "<img src=\"" . $logoSrc . "\" class=\"v-middle\" />&nbsp;";

        // TODO(brian): conditionally display based on payment type
        if (true) {
          $html.= "Buy Now and Pay Later";
        } else {
          $html.= "Buy with monthly payments";
        }

        $this->setMethodLabelAfterHtml($html);
    }
}
