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
 * Class Affirm_Affirm_Model_Credential_Abstract
 */
abstract class Affirm_Affirm_Model_Credential_Abstract
{
    /**
     * Get api url
     *
     * @return string
     */
    abstract public function getApiUrl();

    /**
     * Get api key
     *
     * @return string
     */
    abstract public function getApiKey();

    /**
     * Get secret key
     *
     * @return string
     */
    abstract public function getSecretKey();
}
