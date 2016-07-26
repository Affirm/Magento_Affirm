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
 * Class Affirm_Affirm_Model_Credential_Sandbox
 */
class Affirm_Affirm_Model_Credential_Sandbox extends Affirm_Affirm_Model_Credential_Abstract
{
    /**
     * Payment affirm api url
     */
    const PAYMENT_AFFIRM_API_URL = 'payment/affirm/api_url_sandbox';

    /**
     * Payment affirm api key
     */
    const PAYMENT_AFFIRM_API_KEY = 'payment/affirm/api_key_sandbox';

    /**
     * Payment affirm secret key
     */
    const PAYMENT_AFFIRM_SECRET_KEY = 'payment/affirm/secret_key_sandbox';

    /**
     * Payment affirm financial product key
     */
    const PAYMENT_AFFIRM_FINANCIAL_PRODUCT_KEY = 'payment/affirm/financial_product_key_sandbox';

    /**
     * Get api url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiUrl($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_API_URL, $store);
    }

    /**
     * Get api key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiKey($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_API_KEY, $store);
    }

    /**
     * Get secret key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getSecretKey($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_SECRET_KEY, $store);
    }

    /**
     * Get secret key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getFinancialProductKey($store = null)
    {
        return Mage::getStoreConfig(self::PAYMENT_AFFIRM_FINANCIAL_PRODUCT_KEY, $store);
    }
}
