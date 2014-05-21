<?php

require_once Mage::getBaseDir('lib').DS.'Affirm'.DS.'Affirm.php';

class Affirm_Affirm_Model_Pricer
{
    public function getPriceInCents($order_item)
    {
        return Affirm_Util::formatCents($order_item->getPrice());
    }
}
