<?php
class Affirm_Affirm_Block_Payment_Info extends Mage_Payment_Block_Info
{

    protected function _toHtml()
    {
        $html = '<a href="https://www.affirm.com/u/">Affirm Split Pay</a>';
        return $html;
    }

}
