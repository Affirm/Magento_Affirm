<?php
/**
 * OnePica
 *
 * NOTICE OF LICENSE
 *
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
class Affirm_Affirm_Model_Source_MfpType extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
    /**#@+
     * Comments
     */
        const FINANCING_PROGRAM_EXCLUSIVELY = 0;
        const FINANCING_PROGRAM_INCLUSIVELY = 1;
    /**#@-*/

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('affirm')->__('Exclusive'),
                    'value' => self::FINANCING_PROGRAM_EXCLUSIVELY
                ),
                array(
                    'label' => Mage::helper('affirm')->__('Inclusive'),
                    'value' => self::FINANCING_PROGRAM_INCLUSIVELY
                )
            );
        }
        return $this->_options;
    }
}
