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

class Affirm_Affirm_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    private $_minMPP = null;

    private function _getMinMPP()
    {
        if($this->_minMPP == null) {
            $this->_minMPP = $this->helper('affirm/promo_asLowAs')->getMinMPP();
            if(empty($this->_minMPP)) {
                $this->_minMPP = 0;
            }
        }

        return $this->_minMPP;
    }

    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $html = parent::getPriceHtml($product, $product, $idSuffix);

        if ($this->helper('affirm/promo_asLowAs')->isAsLowAsDisabledOnPLP()) {
            return $html;
        }

        $mpp = $this->_getMinMPP();

        $price = $product->getFinalPrice();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $price = Mage::getModel('bundle/product_price')->getTotalPrices($product,'min',1);
        } else if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
            $price = $product->getMinimalPrice();
        }
        if ($price > $mpp) {
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

            $mfpValue = $this->helper('affirm/promo_asLowAs')->getAffirmMFPValue(array($productItemMFP), $categoryIds);

            $learnMore = ($this->helper('affirm/promo_asLowas')->isVisibleLearnMore()) ? 'true' : 'false';
            $html .= '<div class="affirm-as-low-as" ' . (!empty($mfpValue) ? 'data-promo-id="' . $mfpValue . '"' : '') . ' data-amount="' . $this->helper('affirm/util')->formatCents($price) .'" data-learnmore-show="'.$learnMore.'"></div>';
        }

        return $html;
    }
}