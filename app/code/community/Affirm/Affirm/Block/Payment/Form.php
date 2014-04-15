<?php
class Affirm_Affirm_Block_Payment_Form extends Mage_Payment_Block_Form
{
    protected function _toHtml()
    {
        $method = $this->getMethod();
        return "Put it on my affirm tab!";
    }
}   
