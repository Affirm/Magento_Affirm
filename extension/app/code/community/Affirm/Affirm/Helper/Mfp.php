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
     * Customer MFP
     *
     * @var string
     */
    protected $customerMfp;

    /**
     * Product MFP
     *
     * @var string
     */
    protected $productMfp;

    /**
     * Category MFP
     *
     * @var string
     */
    protected $categoryMfp;

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
     * Is dynamically set MFP
     *
     * @return string
     */
    public function getDynamicallySetMFP()
    {
        if (null === $this->customerMfp) {
            $customerSession = Mage::getSingleton('customer/session');
            if ($customerSession->isLoggedIn()) {
                $this->customerMfp = $customerSession->getCustomer()->getAffirmCustomerMfp();
            } else {
                $this->customerMfp =  $customerSession->getAffirmCustomerMfp();
            }
        }
        return $this->customerMfp;
    }

    /**
     * Get MFP from quote products
     *
     * @param array $productItemsMFP
     * @return string
     */
    protected function _getMFPFromProducts(array $productItemsMFP)
    {
        if (null === $this->productMfp) {
            if (!empty($productItemsMFP) && (count(array_unique($productItemsMFP)) == 1)) {
                $this->productMfp = reset($productItemsMFP);
            } else{
                $this->productMfp = '';
            }
        }
        return $this->productMfp;
    }

    /**
     * Get MFP from products categories
     *
     * @param array $categoryItemsIds
     * @return string
     */
    protected function _getMFPFromCategories(array $categoryItemsIds)
    {
        if (null === $this->categoryMfp) {
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect(array('affirm_category_mfp'))
                ->addAttributeToFilter('entity_id', array('in' => $categoryItemsIds));
            $categoryItemsMFP = array();
            foreach ($categories as $category) {
                if ($category->getAffirmCategoryMfp()) {
                    $categoryItemsMFP[] = $category->getAffirmCategoryMfp();
                }
            }
            if (!empty($categoryItemsMFP) && (count(array_unique($categoryItemsMFP)) == 1)) {
                $this->categoryMfp = reset($categoryItemsMFP);
            } else {
                $this->categoryMfp = '';
            }
        }
        return $this->categoryMfp;
    }

    /**
     * Get affirm MFP value
     *
     * @param array $productItemsMFP
     * @param array $categoryItemsIds
     * @return string
     */
    public function getAffirmMFPValue(array $productItemsMFP, array $categoryItemsIds)
    {
        $dynamicallyMFPValue = $this->getDynamicallySetMFP();
        if (!empty($dynamicallyMFPValue)) {
            return $dynamicallyMFPValue;
        } elseif ($this->_getMFPFromProducts($productItemsMFP)) {
            return $this->_getMFPFromProducts($productItemsMFP);
        } elseif ($this->_getMFPFromCategories($categoryItemsIds)) {
            return $this->_getMFPFromCategories($categoryItemsIds);
        } elseif ($this->_isMFPValidCurrentDate()) {
            return $this->getMFPDateRange();
        } else {
            return $this->getMFPDefault();
        }
    }
}