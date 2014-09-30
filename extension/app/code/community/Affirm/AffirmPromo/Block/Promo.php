<?php
class Affirm_AffirmPromo_Block_Promo extends Mage_Core_Block_Template
{

  protected function _toHtml()
  {
    return Mage::helper('affirmpromo')->getSnippetCode();
  }

}
