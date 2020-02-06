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
    const PROMO_AFFIRM_FINANCING_PROGRAM_VALUE_DEFAULT = 'affirmpromo/mfp/financing_program_value_default';

    /**
     * Promo ID default value
     */
    const PROMO_AFFIRM_PROMO_ID_VALUE_DEFAULT = 'affirmpromo/as_low_as/promo_id_value_default';

    /**
     * MFP value
     */
    const PROMO_AFFIRM_FINANCING_PROGRAM_VALUE = 'affirmpromo/mfp/financing_program_value';

    /**
     * Promo ID value
     */
    const PROMO_AFFIRM_PROMO_ID_VALUE = 'affirmpromo/mfp/promo_id_value';

    /**
     * Start date
     */
    const PROMO_AFFIRM_START_DATE_MFP = 'affirmpromo/mfp/start_date_mfp';

    /**
     * End date
     */
    const PROMO_AFFIRM_END_DATE_MFP = 'affirmpromo/mfp/end_date_mfp';

    /**
     * Cart size
     */
    const PROMO_AFFIRM_CART_SIZE_MFP_VALUE = 'affirmpromo/mfp/cart_size_mfp_value';

    /**
     * Cart size Promo ID
     */
    const PROMO_AFFIRM_CART_SIZE_PROMO_ID_VALUE = 'affirmpromo/mfp/cart_size_promo_id_value';

    /**
     * Min order total MFP
     */
    const PROMO_AFFIRM_MIN_ORDER_TOTAL_MFP = 'affirmpromo/mfp/min_order_total_mfp';

    /**
     * Max order total MFP
     */
    const PROMO_AFFIRM_MAX_ORDER_TOTAL_MFP = 'affirmpromo/mfp/max_order_total_mfp';

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
     * Product date range MFP
     *
     * @var string
     */
    protected $_productDateRangeMfp;

    /**
     * Category MFP
     *
     * @var string
     */
    protected $_categoryMfp;

    /**
     * Category MFPALS
     *
     * @var string
     */
    protected $_categoryMfpALS = array();

    /**
     * Category date range MFP
     *
     * @var string
     */
    protected $_categoryDateRangeMfp;

    /**
     * Category date range MFPALS
     *
     * @var string
     */
    protected $_categoryDateRangeMfpALS;

    /**
     * Get MFP default
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getMFPDefault($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_FINANCING_PROGRAM_VALUE_DEFAULT, $store);
    }

    /**
     * Get PromoId default
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getPromoIdDefault($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_PROMO_ID_VALUE_DEFAULT, $store);
    }

    /**
     * Get PromoId for date range
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getMFPDateRange($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_FINANCING_PROGRAM_VALUE, $store);
    }


    /**
     * Get PromoId for date range
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getPromoIdDateRange($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_PROMO_ID_VALUE, $store);
    }

    /**
     * Get mfp start date
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getMFPStartDate($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_START_DATE_MFP, $store);
    }

    /**
     * Get mfp end date
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getMFPEndDate($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_END_DATE_MFP, $store);
    }

    /**
     * Get mfp cart size
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getMFPCartSizeValue($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_CART_SIZE_MFP_VALUE, $store);
    }

    /**
     * Get PromoId cart size
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getPromoIdCartSizeValue($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_CART_SIZE_PROMO_ID_VALUE, $store);
    }

    /**
     * Get min order total MFP
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getMinOrderTotalMFP($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_MIN_ORDER_TOTAL_MFP, $store);
    }

    /**
     * Get max order total MFP
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getMaxOrderTotalMFP($store = null)
    {
        if ($store === null) {
            $store = Mage::app()->getStore()->getId();
        }

        return Mage::getStoreConfig(self::PROMO_AFFIRM_MAX_ORDER_TOTAL_MFP, $store);
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
     * Get MFP from entities
     *
     * @param array $entityItemsMFP
     * @return string
     */
    protected function _getMfpFromEntityItemsByDateRange(array $entityItemsMFP)
    {
        $exclusiveMFP = array(); $inclusiveMFP = array(); $existItemWithoutMFP = false;
        $inclusiveMFPTemp = array(); $this->_entityMfp = '';
        foreach ($entityItemsMFP as $entityItemMFP) {
            if (!$entityItemMFP['timebased_value']) {
                $existItemWithoutMFP = true;
            } else {
                if (Mage::app()->getLocale()->isStoreDateInInterval(null, $entityItemMFP['start_date'], $entityItemMFP['end_data'])) {
                    if (!$entityItemMFP['type']) {
                        if (!in_array($entityItemMFP['timebased_value'], $exclusiveMFP)) {
                            $exclusiveMFP[] = $entityItemMFP['timebased_value'];
                        }
                    } else {
                        if (!in_array($entityItemMFP['timebased_value'], $inclusiveMFPTemp)) {
                            $inclusiveMFPTemp[] = $entityItemMFP['timebased_value'];
                            $inclusiveMFP[] = array(
                                'value' => $entityItemMFP['timebased_value'],
                                'priority' => $entityItemMFP['timebased_value']
                            );
                        }
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
     * Get MFP from quote products by date range
     *
     * @param array $productItemsMFP
     * @return string
     */
    protected function _getMFPFromProductsByDateRange(array $productItemsMFP)
    {
        if (null === $this->_productDateRangeMfp) {
            $this->_productDateRangeMfp = $this->_getMfpFromEntityItemsByDateRange($productItemsMFP);
        }
        return $this->_productDateRangeMfp;
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
                    array('affirm_category_mfp', '', 'affirm_category_mfp_type', 'affirm_category_mfp_priority', 'affirm_category_mfp_start_date', 'affirm_category_mfp_end_date')
                )
                ->addAttributeToFilter('entity_id', array('in' => $categoryItemsIds));
            $categoryItemsMFP = array();
            foreach ($categories as $category) {
                $start_date = $category->getAffirmCategoryMfpStartDate();
                $end_date = $category->getAffirmCategoryMfpEndDate();
                if(empty($start_date) || empty($end_date)) {
                    $mfpValue = $category->getAffirmCategoryMfp();
                } else {
                    if(Mage::app()->getLocale()->isStoreDateInInterval(null, $start_date, $end_date)) {
                        $mfpValue = $category->getAffirmCategoryMfp();
                    } else {
                        $mfpValue = "";
                    }
                }

                $categoryItemsMFP[] = array(
                    'value' => $mfpValue,
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
     * Get MFP from products categories by date range
     *
     * @param array $categoryItemsIds
     * @return string
     */
    protected function _getMFPFromCategoriesByDateRange(array $categoryItemsIds)
    {
        if (null === $this->_categoryDateRangeMfp) {
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect(
                    array('affirm_category_mfp', '', 'affirm_category_mfp_type', 'affirm_category_mfp_priority', 'affirm_category_mfp_start_date', 'affirm_category_mfp_end_date')
                )
                ->addAttributeToFilter('entity_id', array('in' => $categoryItemsIds));
            $categoryItemsMFP = array();
            foreach ($categories as $category) {
                $categoryItemsMFP[] = array(
                    'value' => $category->getAffirmCategoryMfp(),
                    'type' => $category->getAffirmCategoryMfpType(),
                    'priority' => $category->getAffirmCategoryMfpPriority() ?
                        $category->getAffirmCategoryMfpPriority() : 0,
                    'start_date' => $category->getAffirmCategoryMfpSatrtDate(),
                    'end_date' => $category->getAffirmCategoryMfpEndDate(),
                );
            }

            $this->_categoryDateRangeMfp = $this->_getMfpFromEntityItemsByDateRange($categoryItemsMFP);
        }
        return $this->_categoryDateRangeMfp;
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
     * Is PromoId based on the cart size
     *
     * @param int $cartTotal
     * @return bool
     */
    public function isPromoIdBasedOnCartSize($cartTotal)
    {
        $minTotal = $this->getMinOrderTotalMFP();
        $maxTotal = $this->getMaxOrderTotalMFP();
        if (!$this->getPromoIdCartSizeValue() || (!empty($minTotal) && $cartTotal < $minTotal || !empty($maxTotal) && $cartTotal > $maxTotal)) {
            return false;
        }
        return true;
    }

    /**
     * Get MFP from quote product ALS
     *
     * @param $productItemMFP
     * @return string
     */
    public function getMFPFromProductALS($productItemMFP)
    {
        return $this->_getMfpFromEntityItems($productItemMFP);
    }

    /**
     * Get MFP from quote products by date range ALS
     *
     * @param array $productItemsMFP
     * @return string
     */
    public function getMFPFromProductByDateRangeALS($productItemMFP)
    {
        return $this->_getMfpFromEntityItemsByDateRange(array($productItemMFP));
    }

    /**
     * Get MFP from products categories ALS
     *
     * @param array $categoryItemsIds
     * @return string
     */
    public function getMFPFromCategoriesALS(array $categoryItemsIds)
    {
        $newCategories = array_diff($categoryItemsIds, array_keys($this->_categoryMfpALS));

        if(sizeof($newCategories)) {
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect(
                    array('affirm_category_promo_id', 'affirm_category_mfp_type', 'affirm_category_mfp_priority', 'affirm_category_mfp_start_date', 'affirm_category_mfp_end_date')
                )
                ->addAttributeToFilter('entity_id', array('in' => $newCategories));
            foreach ($categories as $category) {

                $start_date = $category->getAffirmCategoryMfpStartDate();
                $end_date = $category->getAffirmCategoryMfpEndDate();
                if(empty($start_date) || empty($end_date)) {
                    $mfpValue = $category->getAffirmCategoryPromoId();
                } else {
                    if(Mage::app()->getLocale()->isStoreDateInInterval(null, $start_date, $end_date)) {
                        $mfpValue = $category->getAffirmCategoryPromoId();
                    } else {
                        $mfpValue = "";
                    }
                }

                $this->_categoryMfpALS[$category->getId()] = array(
                    'value' => $mfpValue,
                    'type' => $category->getAffirmCategoryMfpType(),
                    'priority' => $category->getAffirmCategoryMfpPriority() ?
                        $category->getAffirmCategoryMfpPriority() : 0
                );
            }
        }

        $categoryItemsMFP = array_intersect_key($this->_categoryMfpALS, array_flip($categoryItemsIds));

        return $this->_getMfpFromEntityItems($categoryItemsMFP);
    }

    /**
     * Get MFP from products categories by date range ALS
     *
     * @param array $categoryItemsIds
     * @return string
     */
    public function getMFPFromCategoriesByDateRangeALS(array $categoryItemsIds)
    {
        $newCategories = array_diff($categoryItemsIds, array_keys($this->_categoryDateRangeMfpALS));

        if(sizeof($newCategories)) {
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect(
                    array('affirm_category_mfp', '', 'affirm_category_mfp_type', 'affirm_category_mfp_priority', 'affirm_category_mfp_start_date', 'affirm_category_mfp_end_date')
                )
                ->addAttributeToFilter('entity_id', array('in' => $newCategories));
            foreach ($categories as $category) {
                $this->_categoryDateRangeMfpALS[$category->getId()] = array(
                    'value' => $category->getAffirmCategoryMfp(),
                    'type' => $category->getAffirmCategoryMfpType(),
                    'priority' => $category->getAffirmCategoryMfpPriority() ?
                        $category->getAffirmCategoryMfpPriority() : 0,
                    'timebased_value' => $category->getAffirmCategoryMfpTimebase(),
                    'start_date' => $category->getAffirmCategoryMfpSatrtDate(),
                    'end_date' => $category->getAffirmCategoryMfpEndDate(),
                );
            }
        }

        $categoryItemsMFP = array_intersect_key($this->_categoryDateRangeMfpALS, array_flip($categoryItemsIds));

        return $this->_getMfpFromEntityItemsByDateRange($categoryItemsMFP);
    }

    /**
     * Is MFP valid for current date ALS
     *
     * @return bool
     */
    public function isMFPValidCurrentDateALS()
    {
        return $this->getPromoIdDateRange() &&
            Mage::app()->getLocale()->isStoreDateInInterval(null, $this->getMFPStartDate(), $this->getMFPEndDate());
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
