<?php
/**
 * OnePica
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to codemaster@onepica.com so we can send you a copy immediately.
 *
 * @category    Affirm
 * @package     Affirm_Affirm
 * @copyright   Copyright (c) 2014 One Pica, Inc. (http://www.onepica.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Affirm As Low As cart link
 */
class Affirm_Affirm_Block_Promo_AsLowAs_Checkout extends Mage_Core_Block_Template
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->helper('affirm/promo_asLowAs')->isAsLowAsDisabledOnCheckout()) {
            return "";
        }

        $mpp = $this->helper('affirm/promo_asLowAs')->getMinMPP();
        if (!empty($mpp)) {
            if ($this->helper('checkout/cart')->getQuote()->getGrandTotal() < $mpp) {
                return "";
            }
        }

        return parent::_toHtml();
    }

    /**
     * Get grand total for cart
     *
     * @return int
     */
    public function getCheckoutGrandTotal()
    {
        $total = $this->helper('checkout/cart')->getQuote()->getGrandTotal();
        return $this->helper('affirm/util')->formatCents($total);
    }

    /**
     * Get MFP value
     *
     * @return string
     */
    public function getMFPValue()
    {
        $cart = Mage::getModel('checkout/cart')->getQuote();
        $productIds = array();
        $productItemMFP = array();
        $categoryItemsIds = array();
        foreach ($cart->getAllVisibleItems() as $item) {
            $productIds[] = $item->getProduct()->getId();
        }

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect(
                array('affirm_product_promo_id', 'affirm_product_mfp_type', 'affirm_product_mfp_priority')
            )
            ->addAttributeToFilter('entity_id', array('in' => $productIds));
        $productItems = $products->getItems();

        foreach ($cart->getAllVisibleItems() as $item) {
            $product = $productItems[$item->getProduct()->getId()];
            if (Mage::helper('affirm')->isPreOrder() && $item->getProduct()->getParentItem() &&
                ($item->getProduct()->getParentItem()->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            ) {
                continue;
            }

            $start_date = $product->getAffirmProductMfpStartDate();
            $end_date = $product->getAffirmProductMfpEndDate();
            if(empty($start_date) || empty($end_date)) {
                $mfpValue = $product->getAffirmProductPromoId();
            } else {
                if(Mage::app()->getLocale()->isStoreDateInInterval(null, $start_date, $end_date)) {
                    $mfpValue = $product->getAffirmProductPromoId();
                } else {
                    $mfpValue = "";
                }
            }

            $productItemMFP[] = array(
                'value' => $mfpValue,
                'type' => $product->getAffirmProductMfpType(),
                'priority' => $product->getAffirmProductMfpPriority() ?
                    $product->getAffirmProductMfpPriority() : 0
            );

            $categoryIds = $product->getCategoryIds();
            if (!empty($categoryIds)) {
                $categoryItemsIds = array_merge($categoryItemsIds, $categoryIds);
            }
        }

        $categoryIds = $product->getCategoryIds();

        return Mage::helper('affirm/promo_asLowAs')->getAffirmMFPValue($productItemMFP, $categoryIds, $this->helper('checkout/cart')->getQuote()->getGrandTotal());
    }
}
