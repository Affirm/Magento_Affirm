<?php
class Affirm_AffirmPromo_Block_Promo extends Mage_Core_Block_Template
{
    /**
     * Build an promos html code output
     * @return mixed
     */
    public function getPromoCode()
    {    	
        return Mage::helper('affirmpromo')->getSnippetCode();
    }

}