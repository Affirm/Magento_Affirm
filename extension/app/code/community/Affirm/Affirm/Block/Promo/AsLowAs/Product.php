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
 * Affirm As Low As pdp link
 */
class Affirm_Affirm_Block_Promo_AsLowAs_Product extends Mage_Core_Block_Template
{
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->helper('affirm/promo_asLowAs')->isAsLowAsDisabledOnPDP()) {
            return "";
        }

        $mpp = $this->helper('affirm/promo_asLowAs')->getMinMPP();
        if (!empty($mpp)) {
            if ($this->getFinalPrice() < $mpp) {
                return "";
            }
        }
        return parent::_toHtml();
    }

    /**
     * Get product on PDP
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return $this->helper('catalog')->getProduct();
    }

    /**
     * Get product id on PDP
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProductId()
    {
        return $this->helper('catalog')->getProduct()->getId();
    }

    /**
     * Get final price
     *
     * @return int
     */
    public function getFinalPrice()
    {
        $product = $this->getProduct();
        if($product->getFinalPrice()) {
            return $this->helper('affirm/util')->formatCents($product->getFinalPrice());
        } else if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $optionCol= $product->getTypeInstance(true)
                ->getOptionsCollection($product);
            $selectionCol= $product->getTypeInstance(true)
                ->getSelectionsCollection(
                    $product->getTypeInstance(true)->getOptionsIds($product),
                    $product
                );
            $optionCol->appendSelections($selectionCol);
            $price = $product->getPrice();

            foreach ($optionCol as $option) {
                if($option->required) {
                    $selections = $option->getSelections();
                    $minPrice = min(array_map(function ($s) {
                        return $s->price;
                    }, $selections));
                    if($product->getSpecialPrice() > 0) {
                        $minPrice *= $product->getSpecialPrice()/100;
                    }

                    $price += round($minPrice,2);
                }
            }
            return $this->helper('affirm/util')->formatCents($price);
        } else {
            return "";
        }
    }

    /**
     * Get MFP value
     *
     * @return string
     */
    public function getMFPValue()
    {
        $product = $this->getProduct();

        $categoryIds = $product->getCategoryIds();

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

        $productItemMFP = array(
            'value' => $mfpValue,
            'type' => $product->getAffirmProductMfpType(),
            'priority' => $product->getAffirmProductMfpPriority() ?
                $product->getAffirmProductMfpPriority() : 0
        );

        return $this->helper('affirm/promo_asLowAs')->getAffirmMFPValue(array($productItemMFP), $categoryIds);
    }
}
