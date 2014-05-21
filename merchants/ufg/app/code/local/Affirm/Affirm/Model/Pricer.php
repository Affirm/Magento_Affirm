<?php

require_once Mage::getBaseDir('lib').DS.'Affirm'.DS.'Affirm.php';

/*
 * Pricer for UFG's custom price model for bundled products.
 */
class Affirm_Affirm_Model_Pricer
{
    public function getPriceInCents($order_item)
    {
        if ($order_item->getProductType() ===
            Mage_Catalog_Model_Product_Type::TYPE_BUNDLE)
        {
            $sum = 0;
            foreach ($order_item->getChildrenItems() as $ch) {
                $cents = Affirm_Util::formatCents($ch->getPrice());
                $sum = $sum + $ch->getQtyToInvoice() * $cents;
            }
            return $sum;
        }
        return Affirm_Util::formatCents($order_item->getPrice());
    }

    private function _getProduct($order_item)
    {
        // TODO(brian): OPTIMIZE consider keeping a handle as an instance
        // variable. This is expensive to call for each item.
        $products = Mage::getModel('catalog/product');

        $productId = $order_item->getProductId();
        return $products->load($productId);
    }
}
