<?php

/*
 * Pricer for UFG's custom price model for bundled products.
 */
class Affirm_Affirm_Model_Pricer
{
    public function getPrice($order_item)
    {
        if ($order_item->getProductType() ===
            Mage_Catalog_Model_Product_Type::TYPE_BUNDLE)
        {
            $bundle_product = $this->_getProduct($order_item);
            list($list_price, $our_price) =
                $bundle_product->getPriceModel()->getTypicalPrices($bundle_product);
            return $our_price;
        }
        return $order_item->getPrice();
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
