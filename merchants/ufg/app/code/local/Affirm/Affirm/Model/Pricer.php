<?php

class Affirm_Affirm_Model_Pricer
{
    // TODO(brian): impl for UFG
    public function getPrice($order_item)
    {
        return $order_item->getPrice();
    }
}
