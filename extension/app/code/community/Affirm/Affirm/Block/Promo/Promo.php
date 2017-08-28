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
class Affirm_Affirm_Block_Promo_Promo extends Mage_Core_Block_Template
{
    /**
     * Helper
     *
     * @var Affirm_Affirm_Helper_Promo_Data
     */
    protected $_helper;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('affirm/promo_data');
        parent::_construct();
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
     * Get MFP value
     *
     * @return string
     */
    public function getMFPValue()
    {
        $currentController = Mage::app()->getFrontController()->getRequest()->getControllerName();
        $mfpHelper = Mage::helper('affirm/mfp');
        switch($currentController) {
            case 'product':
                return $this->getProductMFPValue();
                break;
            case 'category':
                $categoryId = Mage::registry('current_category')->getId();
                $categoryIds = array();
                $categoryIds[] = $categoryId;
                return $this->helper('affirm/promo_asLowAs')->getAffirmMFPValue(array(), $categoryIds);
                break;
            case 'cart':
                return $this->getCheckoutMFPValue();
                break;
            default:
                if ($mfpHelper->isMFPValidCurrentDateALS()) {
                    return $mfpHelper->getPromoIdDateRange();
                } else {
                    return $mfpHelper->getPromoIdDefault();
                }
                break;
        }
    }

    /**
     * Get Product MFP value
     *
     * @return string
     */
    public function getProductMFPValue()
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

    /**
     * Get Cart MFP value
     *
     * @return string
     */
    public function getCheckoutMFPValue()
    {
        $cart = Mage::getModel('checkout/cart')->getQuote();
        $productIds = array();
        $productItemMFP = array();
        $categoryItemsIds = array();
        $categoryIds = array();
        foreach ($cart->getAllVisibleItems() as $item) {
            $productIds[] = $item->getProduct()->getId();
        }
        if($productIds) {
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
                if (empty($start_date) || empty($end_date)) {
                    $mfpValue = $product->getAffirmProductPromoId();
                } else {
                    if (Mage::app()->getLocale()->isStoreDateInInterval(null, $start_date, $end_date)) {
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
        }

        return Mage::helper('affirm/promo_asLowAs')->getAffirmMFPValue($productItemMFP, $categoryIds, $this->helper('checkout/cart')->getQuote()->getGrandTotal());
    }
}
