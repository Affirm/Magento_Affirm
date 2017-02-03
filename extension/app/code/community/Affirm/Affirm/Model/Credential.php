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
 * Class Affirm_Affirm_Model_Credential
 */
class Affirm_Affirm_Model_Credential
{
    /**
     * Payment affirm account mode
     */
    const PAYMENT_AFFIRM_ACCOUNT_MODE = 'payment/affirm/account_mode';

    /**
     * Account mode sandbox
     */
    const ACCOUNT_MODE_SANDBOX = 'sandbox';

    /**
     * Account mode production
     */
    const ACCOUNT_MODE_PRODUCTION = 'production';
    /**
     * Info block type
     *
     * @var array
     */
    protected $_credentialModelsCache;

    /**
     * Get store id for cache
     *
     * @param Mage_Core_Model_Store|int|null $store
     * @return int
     */
    protected function _getStoreIdForCache($store = null)
    {
        $id = null;
        if ($store instanceof Mage_Core_Model_Store) {
            $id = $store->getStoreId();
        }
        if (!$id) {
            $id = $store ? $store : 0;
        }
        return $id;
    }

    /**
     * Get credential model due to current account type
     *
     * @param Mage_Core_Model_Store $store
     * @return mixed
     * @throws Affirm_Affirm_Exception
     */
    protected function _getCredentialModel($store = null)
    {
        $storeCacheId = $this->_getStoreIdForCache($store);
        if (!isset($this->_credentialModelsCache[$storeCacheId])) {
            $mode = Mage::getStoreConfig(self::PAYMENT_AFFIRM_ACCOUNT_MODE, $store);
            $modelClass = 'affirm/credential_' . $mode;
            $model = Mage::getModel($modelClass);
            if (!$model) {
                throw new Affirm_Affirm_Exception('Could not found model ' . $modelClass);
            }
            $this->_credentialModelsCache[$storeCacheId] = $model;
        }
        return $this->_credentialModelsCache[$storeCacheId];
    }

    /**
     * Get api url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiUrl($store = null)
    {
        return $this->_getCredentialModel($store)->getApiUrl($store);
    }

    /**
     * Get api key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getApiKey($store = null)
    {
        return $this->_getCredentialModel($store)->getApiKey($store);
    }

    /**
     * Get secret key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getSecretKey($store = null)
    {
        return $this->_getCredentialModel($store)->getSecretKey($store);
    }

    /**
     * Get secret key
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getFinancialProductKey($store = null)
    {
        return $this->_getCredentialModel($store)->getFinancialProductKey($store);
    }
}
