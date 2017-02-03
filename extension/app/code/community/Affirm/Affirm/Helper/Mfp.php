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
 * Class Affirm_Affirm_Helper_Mfp
 */
class Affirm_Affirm_Helper_Mfp extends Mage_Core_Helper_Abstract
{
    /**
     * MFP default value
     */
    const PAYMENT_AFFIRM_FINANCING_PROGRAM_VALUE_DEFAULT = 'payment/affirm/financing_program_value_default';

    /**
     * MFP value
     */
    const PAYMENT_AFFIRM_FINANCING_PROGRAM_VALUE = 'payment/affirm/financing_program_value';

    /**
     * Start date
     */
    const PAYMENT_AFFIRM_START_DATE_MFP = 'payment/affirm/start_date_mfp';

    /**
     * End date
     */
    const PAYMENT_AFFIRM_END_DATE_MFP = 'payment/affirm/end_date_mfp';

    /**
     * Cart size
     */
    const PAYMENT_AFFIRM_CART_SIZE_MFP_VALUE = 'payment/affirm/cart_size_mfp_value';

    /**
     * Min order total MFP
     */
    const PAYMENT_AFFIRM_MIN_ORDER_TOTAL_MFP = 'payment/affirm/min_order_total_mfp';

    /**
     * Max order total MFP
     */
    const PAYMENT_AFFIRM_MAX_ORDER_TOTAL_MFP = 'payment/affirm/max_order_total_mfp';

    /**
     * Customer MFP
     *
     * @var string
     */
    protected $_customerMfp;

    /**
     * Entity MFP
     *
     * @var string
     */
    protected $_entityMfp;

    /**
     * Product MFP
     *
     * @var string
     */
    protected $_productMfp;

    /**
     * Category MFP
     *
     * @var string
     */
    protected $_categoryMfp;

    /**
     * Get MFP default
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMFPDefault($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_FINANCING_PROGRAM_VALUE_DEFAULT, $store);
    }

    /**
     * Get MFP for date range
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMFPDateRange($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_FINANCING_PROGRAM_VALUE, $store);
    }

    /**
     * Get mfp start date
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMFPStartDate($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_START_DATE_MFP, $store);
    }

    /**
     * Get mfp end date
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMFPEndDate($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_END_DATE_MFP, $store);
    }

    /**
     * Get mfp cart size
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMFPCartSizeValue($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_CART_SIZE_MFP_VALUE, $store);
    }

    /**
     * Get min order total MFP
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMinOrderTotalMFP($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_MIN_ORDER_TOTAL_MFP, $store);
    }

    /**
     * Get max order total MFP
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getMaxOrderTotalMFP($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_MAX_ORDER_TOTAL_MFP, $store);
    }

    /**
     * Is MFP valid for current date
     *
     * @return bool
     */
    protected function _isMFPValidCurrentDate()
    {
        return $this->getMFPDateRange() &&
            Mage::app()->getLocale()->isStoreDateInInterval(null, $this->getMFPStartDate(), $this->getMFPEndDate());
    }

    /**
     * Get dynamically set MFP
     *
     * @return string
     */
    public function getDynamicallySetMFP()
    {
        if (null === $this->_customerMfp) {
            $customerSession = Mage::getSingleton('customer/session');
            if ($customerSession->isLoggedIn()) {
                $this->_customerMfp = $customerSession->getCustomer()->getAffirmCustomerMfp();
            } else {
                $this->_customerMfp =  $customerSession->getAffirmCustomerMfp();
            }
        }
        return $this->_customerMfp;
    }

    /**
     * Get MFP from entities
     *
     * @param array $entityItemsMFP
     * @return string
     */
    protected function _getMfpFromEntityItems(array $entityItemsMFP)
    {
        $exclusiveMFP = array(); $inclusiveMFP = array(); $existItemWithoutMFP = false;
        $inclusiveMFPTemp = array(); $this->_entityMfp = '';
        foreach ($entityItemsMFP as $entityItemMFP) {
            if (!$entityItemMFP['value']) {
                $existItemWithoutMFP = true;
            } else {
                if (!$entityItemMFP['type']) {
                    if (!in_array($entityItemMFP['value'], $exclusiveMFP)) {
                        $exclusiveMFP[] = $entityItemMFP['value'];
                    }
                } else {
                    if (!in_array($entityItemMFP['value'], $inclusiveMFPTemp)) {
                        $inclusiveMFPTemp[] = $entityItemMFP['value'];
                        $inclusiveMFP[] = array(
                            'value' => $entityItemMFP['value'],
                            'priority' => $entityItemMFP['priority']
                        );
                    }
                }
            }
        }

        if (count($inclusiveMFP) == 1) {
            $this->_entityMfp = $inclusiveMFP[0]['value'];
        } elseif ((count($exclusiveMFP) == 1) && (count($inclusiveMFP) == 0) && !$existItemWithoutMFP) {
            $this->_entityMfp = $exclusiveMFP[0];
        } elseif (count($inclusiveMFP) > 1) {
            $higherPriority = -1;
            foreach ($inclusiveMFP as $inclusiveMFPValue) {
                if ($inclusiveMFPValue['priority'] > $higherPriority) {
                    $higherPriority = $inclusiveMFPValue['priority'];
                    $this->_entityMfp = $inclusiveMFPValue['value'];
                }
            }
        } else {
            $this->_entityMfp = '';
        }
        return $this->_entityMfp;
    }

    /**
     * Get MFP from quote products
     *
     * @param array $productItemsMFP
     * @return string
     */
    protected function _getMFPFromProducts(array $productItemsMFP)
    {
        if (null === $this->_productMfp) {
            $this->_productMfp = $this->_getMfpFromEntityItems($productItemsMFP);
        }
        return $this->_productMfp;
    }

    /**
     * Get MFP from products categories
     *
     * @param array $categoryItemsIds
     * @return string
     */
    protected function _getMFPFromCategories(array $categoryItemsIds)
    {
        if (null === $this->_categoryMfp) {
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect(
                    array('affirm_category_mfp', '', 'affirm_category_mfp_type', 'affirm_category_mfp_priority')
                )
                ->addAttributeToFilter('entity_id', array('in' => $categoryItemsIds));
            $categoryItemsMFP = array();
            foreach ($categories as $category) {
                $categoryItemsMFP[] = array(
                    'value' => $category->getAffirmCategoryMfp(),
                    'type' => $category->getAffirmCategoryMfpType(),
                    'priority' => $category->getAffirmCategoryMfpPriority() ?
                        $category->getAffirmCategoryMfpPriority() : 0
                );
            }

            $this->_categoryMfp = $this->_getMfpFromEntityItems($categoryItemsMFP);
        }
        return $this->_categoryMfp;
    }

    /**
     * Is MFP based on the cart size
     *
     * @param int $cartTotal
     * @return bool
     */
    protected function _isMFPBasedOnCartSize($cartTotal)
    {
        $minTotal = $this->getMinOrderTotalMFP();
        $maxTotal = $this->getMaxOrderTotalMFP();
        if (!$this->getMFPCartSizeValue() || (!empty($minTotal) && $cartTotal < $minTotal || !empty($maxTotal) && $cartTotal > $maxTotal)) {
            return false;
        }
        return true;
    }

    /**
     * Get affirm MFP value
     *
     * @param array $productItemsMFP
     * @param array $categoryItemsIds
     * @param string $cartTotal
     * @return string
     */
    public function getAffirmMFPValue(array $productItemsMFP, array $categoryItemsIds, $cartTotal)
    {
        $dynamicallyMFPValue = $this->getDynamicallySetMFP();
        if (!empty($dynamicallyMFPValue)) {
            return $dynamicallyMFPValue;
        } elseif ($this->_getMFPFromProducts($productItemsMFP)) {
            return $this->_getMFPFromProducts($productItemsMFP);
        } elseif ($this->_getMFPFromCategories($categoryItemsIds)) {
            return $this->_getMFPFromCategories($categoryItemsIds);
        } elseif ($this->_isMFPBasedOnCartSize($cartTotal)) {
            return $this->getMFPCartSizeValue();
        } elseif ($this->_isMFPValidCurrentDate()) {
            return $this->getMFPDateRange();
        } else {
            return $this->getMFPDefault();
        }
    }
}
