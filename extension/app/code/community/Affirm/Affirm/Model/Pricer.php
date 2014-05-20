<?php

class Affirm_Affirm_Model_Pricer
{
    public function getPrice($order_item)
    {
        return $order_item->getPrice();
    }
}
